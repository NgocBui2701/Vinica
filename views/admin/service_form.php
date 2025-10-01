<?php
require_once __DIR__ . '/../../DAO/UserDAO.php';
require_once __DIR__ . '/../../DAO/ServiceDAO.php';
require_once __DIR__ . '/../../DAO/pdo.php';

// Cấu hình upload hình ảnh cho Services
define('SERVICE_IMAGE_UPLOAD_DIR_SERVER', dirname(__DIR__, 3) . '/VINICA/layout/img/uploads/services/'); // Đường dẫn tuyệt đối trên server
define('SERVICE_IMAGE_UPLOAD_DIR_PUBLIC', '/VINICA/layout/img/uploads/services/');
define('MAX_FILE_SIZE_SERVICE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_MIME_TYPES_SERVICE', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_EXTENSIONS_SERVICE', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Kiểm tra và tạo thư mục upload nếu chưa tồn tại
if (!is_dir(SERVICE_IMAGE_UPLOAD_DIR_SERVER)) {
    mkdir(SERVICE_IMAGE_UPLOAD_DIR_SERVER, 0775, true);
}

// Kiểm tra xác thực và quyền admin
$userDAO = new UserDAO();
$loggedInUser = null;
if (isset($_SESSION['user_id'])) {
    $loggedInUser = $userDAO->findById($_SESSION['user_id']);
}

if (!$loggedInUser) {
    header("Location: /VINICA/login");
    exit;
} elseif ($loggedInUser['role'] !== 'admin') {
    header("Location: /VINICA/login");
    exit;
}

$serviceDAO = new ServiceDAO();
$errors = [];
$success_message = '';

// Xác định action: 'add' hoặc 'edit'
// $action được truyền từ $routes trong index.php thông qua extract($params)
if (!isset($action)) { // Phòng trường hợp file được truy cập trực tiếp không qua router
    // Nếu không có action, có thể dựa vào sự tồn tại của service_id để đoán
    if (isset($_GET['id'])) {
        $action = 'edit';
    } else {
        $action = 'add';
    }
}


$service_id = null;
$service_name = '';
$service_slug = '';
$service_description = '';
$service_image_url = '';
$service_display_order = 0;
$service_is_visible = true; // Mặc định là true khi thêm mới

if ($action === 'edit') {
    if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $_SESSION['error_message'] = "Invalid service ID.";
        header("Location: /VINICA/admin-dashboard/service-management");
        exit;
    }
    $service_id = (int)$_GET['id'];
    $service = $serviceDAO->getServiceById($service_id);

    if (!$service) {
        $_SESSION['error_message'] = "Service not found.";
        header("Location: /VINICA/admin-dashboard/service-management");
        exit;
    }
    $service_name = $service['name'];
    $service_slug = $service['slug'];
    $service_description = $service['description'];
    $service_image_url = $service['image_url'];
    $service_display_order = $service['display_order'];
    $service_is_visible = (bool)$service['is_visible'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = trim($_POST['service_name'] ?? '');
    $service_slug = trim($_POST['service_slug'] ?? '');
    $service_description = $_POST['service_description'] ?? ''; // TinyMCE gửi HTML
    $service_image_url_input = trim($_POST['service_image_url'] ?? '');
    $service_display_order = filter_var($_POST['service_display_order'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
    $service_is_visible = isset($_POST['service_is_visible']);

    // Lấy service_id từ hidden input nếu là edit
    if ($action === 'edit' && isset($_POST['service_id'])) {
        $service_id = (int)$_POST['service_id'];
    }

    // Validate
    if (empty($service_name)) {
        $errors['service_name'] = "Service name is required.";
    }

    // Tự động tạo slug nếu rỗng
    if (empty($service_slug) && !empty($service_name)) {
        $service_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $service_name), '-'));
    } elseif (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $service_slug)) {
        $errors['service_slug'] = "Slug can only contain lowercase letters, numbers, and hyphens, and cannot start or end with a hyphen.";
    }

    // Xử lý upload hình ảnh
    $final_image_url = $service_image_url; // Giữ ảnh cũ nếu không có thay đổi

    if (isset($_FILES['service_image_file']) && $_FILES['service_image_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['service_image_file'];
        if ($file['size'] > MAX_FILE_SIZE_SERVICE) {
            $errors['service_image_file'] = "File exceeds the maximum allowed size (2MB).";
        } else {
            $fileMimeType = mime_content_type($file['tmp_name']);
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileMimeType, ALLOWED_MIME_TYPES_SERVICE) || !in_array($fileExtension, ALLOWED_EXTENSIONS_SERVICE)) {
                $errors['service_image_file'] = "Invalid file format. Only JPG, PNG, GIF, WEBP are allowed.";
            } else {
                $newFileName = uniqid('service_', true) . '.' . $fileExtension;
                $destination = SERVICE_IMAGE_UPLOAD_DIR_SERVER . $newFileName;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $final_image_url = SERVICE_IMAGE_UPLOAD_DIR_PUBLIC . $newFileName;
                } else {
                    $errors['service_image_file'] = "Failed to move uploaded file. Check directory permissions.";
                }
            }
        }
    } elseif (!empty($service_image_url_input)) {
         // Kiểm tra URL nhập vào nếu không có file upload
        if (filter_var($service_image_url_input, FILTER_VALIDATE_URL) || (strpos($service_image_url_input, '/') === 0 && file_exists(dirname(__DIR__, 3) . $service_image_url_input))) {
            $final_image_url = $service_image_url_input;
        } else {
            $errors['service_image_url'] = "Invalid image URL or path provided.";
        }
    } elseif ($action === 'add' && empty($final_image_url) && !(isset($_FILES['service_image_file']) && $_FILES['service_image_file']['error'] === UPLOAD_ERR_OK)) {
        // bỏ qua
    }


    if (empty($errors)) {
        $data = [
            'name' => $service_name,
            'slug' => $service_slug,
            'description' => $service_description,
            'image_url' => $final_image_url,
            'display_order' => $service_display_order,
            'is_visible' => $service_is_visible ? 1 : 0,
        ];

        if ($action === 'add') {
            $newServiceWasCreated = $serviceDAO->createService($data);
            if ($newServiceWasCreated) {
                $_SESSION['success_message'] = "Service '{$service_name}' created successfully.";
                header("Location: /VINICA/admin-dashboard/service-management");
                exit;
            } else {
                $errors['database'] = "Failed to create service. Database error.";
            }
        } elseif ($action === 'edit' && $service_id) {
            if ($serviceDAO->updateService($service_id, $data)) {
                $_SESSION['success_message'] = "Service '{$service_name}' updated successfully.";
                header("Location: /VINICA/admin-dashboard/service-management/edit?id={$service_id}");
                exit;
            } else {
                $errors['database'] = "Failed to update service. Database error or no changes made.";
            }
        }
    }
}


$page_title_text = ($action === 'edit' && $service_id) ? "Edit Service: " . htmlspecialchars($service_name) : "Add New Service";
$submit_button_text = ($action === 'edit') ? "Update Service" : "Save Service";

$title = $page_title_text . " | VINICA Admin";
ob_start();
?>

<div class="container mt-4 admin-management-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard/service-management">Manage Services</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $page_title_text; ?></li>
        </ol>
    </nav>
    <h1 class="mb-4 page-title"><?php echo $page_title_text; ?></h1>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($errors['database'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errors['database']); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
        <?php if ($action === 'edit' && $service_id): ?>
            <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
        <?php endif; ?>
        <input type="hidden" name="form_action" value="<?php echo $action; ?>">

        <div class="mb-3">
            <label for="service_name" class="form-label"><strong>Service Name <span class="text-danger">*</span></strong></label>
            <input type="text" class="form-control <?php echo isset($errors['service_name']) ? 'is-invalid' : ''; ?>" id="service_name" name="service_name" value="<?php echo htmlspecialchars($service_name); ?>" required>
            <?php if (isset($errors['service_name'])): ?>
                <div class="invalid-feedback"><?php echo $errors['service_name']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="service_slug" class="form-label"><strong>Slug</strong></label>
            <input type="text" class="form-control <?php echo isset($errors['service_slug']) ? 'is-invalid' : ''; ?>" id="service_slug" name="service_slug" value="<?php echo htmlspecialchars($service_slug); ?>">
            <small class="form-text text-muted">Leave blank to auto-generate from name. Use lowercase letters, numbers, and hyphens.</small>
            <?php if (isset($errors['service_slug'])): ?>
                <div class="invalid-feedback"><?php echo $errors['service_slug']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="service_description" class="form-label"><strong>Description</strong></label>
            <textarea class="form-control tinymce-editor" id="service_description" name="service_description" rows="10"><?php echo htmlspecialchars($service_description); ?></textarea>
            <small class="form-text text-muted">This content will be displayed on the service detail page. Use the rich text editor for formatting.</small>
        </div>

        <div class="mb-3 border p-3 rounded">
            <p class="form-label"><strong>Service Image</strong></p>
            <div class="mb-2">
                <label for="service_image_file" class="form-label"><small>Upload New Image (Optional, Max 2MB, JPG/PNG/GIF/WEBP):</small></label>
                <input type="file" class="form-control <?php echo isset($errors['service_image_file']) ? 'is-invalid' : ''; ?>" id="service_image_file" name="service_image_file" accept=".jpg,.jpeg,.png,.gif,.webp">
                <?php if (isset($errors['service_image_file'])): ?>
                    <div class="invalid-feedback d-block"><?php echo $errors['service_image_file']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-2">
                <label for="service_image_url" class="form-label"><small>Or Enter Image URL:</small></label>
                <input type="url" class="form-control <?php echo isset($errors['service_image_url']) ? 'is-invalid' : ''; ?>" id="service_image_url" name="service_image_url" value="<?php echo htmlspecialchars($service_image_url); ?>" placeholder="https://example.com/image.jpg or /VINICA/path/to/image.jpg">
                 <?php if (isset($errors['service_image_url'])): ?>
                    <div class="invalid-feedback d-block"><?php echo $errors['service_image_url']; ?></div>
                <?php endif; ?>
            </div>
            <?php
            $current_image_to_display = $service_image_url; // Giá trị từ DB hoặc POST trước đó
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_image_url'])) { // Nếu có POST thì ưu tiên giá trị vừa nhập
                $current_image_to_display = htmlspecialchars($_POST['service_image_url']);
            }

            $canPreview = !empty($current_image_to_display) &&
                          (filter_var($current_image_to_display, FILTER_VALIDATE_URL) || (is_string($current_image_to_display) && strpos($current_image_to_display, '/') === 0));

            if ($canPreview):
            ?>
                <div class="mt-2">
                    <p><small>Current Image Preview:</small></p>
                    <img src="<?php echo htmlspecialchars($current_image_to_display); ?>" alt="Current Service Image" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; object-fit: cover;">
                </div>
            <?php elseif ($action === 'edit' && !empty($service['image_url'])): // For edit mode, if POST fails but there was an original image
                 $original_image = htmlspecialchars($service['image_url']);
                 $canPreviewOriginal = !empty($original_image) && (filter_var($original_image, FILTER_VALIDATE_URL) || (is_string($original_image) && strpos($original_image, '/') === 0));
                 if($canPreviewOriginal): ?>
                    <div class="mt-2">
                        <p><small>Current Image Preview (from database):</small></p>
                        <img src="<?php echo $original_image; ?>" alt="Current Service Image" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; object-fit: cover;">
                    </div>
            <?php endif;
            endif; ?>
        </div>


        <div class="mb-3">
            <label for="service_display_order" class="form-label"><strong>Display Order</strong></label>
            <input type="number" class="form-control" id="service_display_order" name="service_display_order" value="<?php echo (int)$service_display_order; ?>">
            <small class="form-text text-muted">Services with lower numbers will appear first.</small>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="service_is_visible" name="service_is_visible" value="1" <?php echo $service_is_visible ? 'checked' : ''; ?>>
            <label class="form-check-label" for="service_is_visible"><strong>Is Visible?</strong> (Show this service on the public website)</label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg"><?php echo $submit_button_text; ?></button>
        <a href="/VINICA/admin-dashboard/service-management" class="btn btn-secondary btn-lg">Cancel</a>
    </form>
</div>

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/vjtkoqa879nbtp28a4qscoij49z7lcy3xc7olzwgvach3ph0/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            plugins: 'code image link lists media table wordcount preview fullscreen help autoresize',
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | ' +
                     'bullist numlist outdent indent | link image media table | code preview fullscreen | help',
            menubar: 'file edit view insert format tools table help',
            height: 450,
            autoresize_bottom_margin: 50,
            // Tự động chuyển đổi URL hình ảnh tương đối sang tuyệt đối khi upload
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
             // Cấu hình để upload ảnh qua TinyMCE (nếu cần - yêu cầu backend xử lý)
            images_upload_url: '/VINICA/admin-dashboard/tinymce_image_upload.php', // Điểm cuối xử lý upload
            images_upload_base_path: '/VINICA/layout/img/uploads/tinymce_uploads/', // Đường dẫn công khai cho ảnh đã upload
            images_upload_credentials: true,
            automatic_uploads: true,
            file_picker_types: 'image',
            file_picker_callback: function(cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function() {
                    var file = this.files[0];
                    var reader = new FileReader();
                    reader.onload = function () {
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);
                        cb(blobInfo.blobUri(), { title: file.name });
                    };
                    reader.readAsDataURL(file);
                };
                input.click();
            }
        });

        // Tự động tạo slug (tùy chọn)
        const nameInput = document.getElementById('service_name');
        const slugInput = document.getElementById('service_slug');

        if (nameInput && slugInput) {
            nameInput.addEventListener('keyup', function() {
                if (slugInput.dataset.edited !== 'true') { // Chỉ tự động cập nhật nếu slug chưa được sửa thủ công
                    let slug = nameInput.value
                        .toLowerCase()
                        .trim()
                        .replace(/\s+/g, '-')           // Thay khoảng trắng bằng gạch nối
                        .replace(/[^\w-]+/g, '')       // Xóa các ký tự không phải chữ, số, gạch nối
                        .replace(/--+/g, '-')          // Thay nhiều gạch nối bằng một
                        .replace(/^-+|-+$/g, '');      // Xóa gạch nối ở đầu/cuối
                    slugInput.value = slug;
                }
            });
            // Đánh dấu slug đã được sửa thủ công để không tự động cập nhật nữa
            slugInput.addEventListener('input', function() {
                slugInput.dataset.edited = 'true';
            });
        }
    });
</script>

<?php
$content = ob_get_clean();
if (file_exists(__DIR__ . '/../main.php')) {
    require __DIR__ . '/../main.php';
} elseif (file_exists(__DIR__ . '/../../views/main.php')) {
    require __DIR__ . '/../../views/main.php';
} else {
    echo "Admin layout file not found.";
}
?> 