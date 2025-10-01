<?php
require_once __DIR__ . '/../../DAO/UserDAO.php';
require_once __DIR__ . '/../../DAO/ServiceDAO.php';
require_once __DIR__ . '/../../DAO/pdo.php';

// Cấu hình upload hình ảnh cho Venues
define('VENUE_IMAGE_UPLOAD_DIR_SERVER', dirname(__DIR__, 3) . '/VINICA/layout/img/uploads/venues/');
define('VENUE_IMAGE_UPLOAD_DIR_PUBLIC', '/VINICA/layout/img/uploads/venues/');
define('MAX_FILE_SIZE_VENUE', 2 * 1024 * 1024); // 2MB
// Định nghĩa hằng số cho phép loại file ảnh (lấy từ service_form.php cho thống nhất)
if (!defined('ALLOWED_MIME_TYPES_SERVICE')) {
    define('ALLOWED_MIME_TYPES_SERVICE', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
}
if (!defined('ALLOWED_EXTENSIONS_SERVICE')) {
    define('ALLOWED_EXTENSIONS_SERVICE', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}


// Kiểm tra và tạo thư mục upload nếu chưa tồn tại
if (!is_dir(VENUE_IMAGE_UPLOAD_DIR_SERVER)) {
    mkdir(VENUE_IMAGE_UPLOAD_DIR_SERVER, 0775, true);
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
    header("Location: /VINICA/admin-dashboard");
    exit;
}

$serviceDAO = new ServiceDAO();
$errors = [];
// $success_message = ''; // Sẽ lấy từ session

// Lấy service_id từ URL và kiểm tra
if (!isset($_GET['service_id']) || !filter_var($_GET['service_id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error_message'] = "Invalid or missing Service ID.";
    header("Location: /VINICA/admin-dashboard/service-management");
    exit;
}
$service_id = (int)$_GET['service_id'];

// Lấy thông tin dịch vụ cha
$parentService = $serviceDAO->getServiceById($service_id);
if (!$parentService) {
    $_SESSION['error_message'] = "Parent Service not found.";
    header("Location: /VINICA/admin-dashboard/service-management");
    exit;
}

// Xác định action: 'add' hoặc 'edit' cho venue
$venue_action = 'add_venue'; // Mặc định là add
$current_venue_id = null;
$venue_data_for_form = [
    'name' => '',
    'capacity' => '',
    'description' => '',
    'image_url' => '',
    'display_order' => 0,
    'is_visible' => true // Mặc định checked khi thêm mới
];

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['venue_id'])) {
    $venue_action = 'edit_venue';
    $current_venue_id = filter_var($_GET['venue_id'], FILTER_VALIDATE_INT);
    if ($current_venue_id) {
        $existing_venue = $serviceDAO->getVenueById($current_venue_id);
        if ($existing_venue && $existing_venue['service_id'] == $service_id) { // Đảm bảo venue thuộc service hiện tại
            $venue_data_for_form = $existing_venue;
            // Chuyển đổi is_visible sang boolean cho checkbox
            $venue_data_for_form['is_visible'] = (bool)$venue_data_for_form['is_visible'];
        } else {
            $_SESSION['error_message'] = "Venue not found or does not belong to this service.";
            header("Location: /VINICA/admin-dashboard/service-management/venues?service_id=" . $service_id);
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Invalid Venue ID for editing.";
        header("Location: /VINICA/admin-dashboard/service-management/venues?service_id=" . $service_id);
        exit;
    }
}


// Xử lý POST (Thêm hoặc Cập nhật Venue)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy action từ hidden input để phân biệt add và edit khi submit form
    $submitted_action = $_POST['venue_form_action'] ?? 'add_venue'; // Sẽ có 'add_venue' hoặc 'edit_venue'

    $venue_name = trim($_POST['venue_name'] ?? '');
    $venue_capacity = trim($_POST['venue_capacity'] ?? '');
    $venue_description = trim($_POST['venue_description'] ?? '');
    $venue_image_url_input = trim($_POST['venue_image_url'] ?? '');
    $venue_display_order = filter_var($_POST['venue_display_order'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
    $venue_is_visible = isset($_POST['venue_is_visible']);
    $final_venue_image_url = ''; // Sẽ được set bên dưới

    // Nếu là edit, lấy image_url hiện tại để giữ lại nếu không có ảnh mới
    $current_image_for_update = $_POST['current_venue_image_url'] ?? '';
    if ($submitted_action === 'edit_venue') {
        $final_venue_image_url = $current_image_for_update;
    }


    if (empty($venue_name)) {
        $errors['venue_name'] = "Venue name is required.";
    }

    // Xử lý upload hình ảnh
    if (isset($_FILES['venue_image_file']) && $_FILES['venue_image_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['venue_image_file'];
        if ($file['size'] > MAX_FILE_SIZE_VENUE) {
            $errors['venue_image_file'] = "File exceeds 2MB.";
        } else {
            $fileMimeType = mime_content_type($file['tmp_name']);
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileMimeType, ALLOWED_MIME_TYPES_SERVICE) || !in_array($fileExtension, ALLOWED_EXTENSIONS_SERVICE)) {
                $errors['venue_image_file'] = "Invalid file format (JPG, PNG, GIF, WEBP).";
            } else {
                // Xóa ảnh cũ nếu upload ảnh mới thành công và là edit mode, và ảnh cũ tồn tại trên server
                if ($submitted_action === 'edit_venue' && !empty($final_venue_image_url) && strpos($final_venue_image_url, VENUE_IMAGE_UPLOAD_DIR_PUBLIC) === 0) {
                    $old_image_path_server = dirname(__DIR__, 3) . $final_venue_image_url;
                    if (file_exists($old_image_path_server)) {
                        unlink($old_image_path_server);
                    }
                }

                $newFileName = uniqid('venue_' . $service_id . '_', true) . '.' . $fileExtension;
                $destination = VENUE_IMAGE_UPLOAD_DIR_SERVER . $newFileName;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $final_venue_image_url = VENUE_IMAGE_UPLOAD_DIR_PUBLIC . $newFileName;
                } else {
                    $errors['venue_image_file'] = "Upload failed.";
                }
            }
        }
    } elseif (!empty($venue_image_url_input)) {
        if (filter_var($venue_image_url_input, FILTER_VALIDATE_URL) || (strpos($venue_image_url_input, '/') === 0 && file_exists(dirname(__DIR__, 3) . $venue_image_url_input))) {
           // Xóa ảnh cũ nếu người dùng nhập URL mới và là edit mode, và ảnh cũ tồn tại trên server (nếu ảnh cũ không phải là URL)
            if ($submitted_action === 'edit_venue' && $venue_image_url_input !== $current_image_for_update && !empty($current_image_for_update) && strpos($current_image_for_update, VENUE_IMAGE_UPLOAD_DIR_PUBLIC) === 0) {
                 $old_image_path_server = dirname(__DIR__, 3) . $current_image_for_update;
                 if (file_exists($old_image_path_server)) {
                     unlink($old_image_path_server);
                 }
            }
            $final_venue_image_url = $venue_image_url_input;
        } else {
            $errors['venue_image_url'] = "Invalid image URL.";
        }
    }
    // Nếu không có file mới, không có URL mới, và là edit mode, thì giữ ảnh cũ ($final_venue_image_url đã được gán ở trên)

    if (empty($errors)) {
        $venue_details = [
            'service_id' => $service_id,
            'name' => $venue_name,
            'capacity' => $venue_capacity,
            'description' => $venue_description,
            'image_url' => $final_venue_image_url,
            'is_visible' => $venue_is_visible ? 1 : 0,
            'display_order' => $venue_display_order
        ];

        if ($submitted_action === 'add_venue') {
            if ($serviceDAO->createVenue($service_id, $venue_name, $venue_capacity, $venue_description, $final_venue_image_url, $venue_is_visible, $venue_display_order)) {
                $_SESSION['success_message'] = "Venue '" . htmlspecialchars($venue_name) . "' added successfully to " . htmlspecialchars($parentService['name']) . ".";
                header("Location: /VINICA/admin-dashboard/service-management/venues?service_id=" . $service_id);
                exit;
            } else {
                $errors['database'] = "Failed to create venue.";
            }
        } elseif ($submitted_action === 'edit_venue') {
            $venue_id_to_update = filter_var($_POST['venue_id'], FILTER_VALIDATE_INT);
            if ($venue_id_to_update) {
                if ($serviceDAO->updateVenue($venue_id_to_update, $venue_details)) {
                    $_SESSION['success_message'] = "Venue '" . htmlspecialchars($venue_name) . "' updated successfully.";
                     // Redirect về trang edit của chính venue đó hoặc về list venues
                    header("Location: /VINICA/admin-dashboard/service-management/venues?service_id=" . $service_id . "&action=edit&venue_id=" . $venue_id_to_update);
                    exit;
                } else {
                    $errors['database'] = "Failed to update venue. Database error or no changes made.";
                }
            } else {
                 $errors['database'] = "Invalid Venue ID for update.";
            }
        }
    }
     // Nếu có lỗi, $errors sẽ được hiển thị và form sẽ được pre-fill với dữ liệu POSTED hoặc dữ liệu edit ban đầu
    if (!empty($errors)) {
        $venue_data_for_form['name'] = $_POST['venue_name'] ?? $venue_data_for_form['name'];
        $venue_data_for_form['capacity'] = $_POST['venue_capacity'] ?? $venue_data_for_form['capacity'];
        $venue_data_for_form['description'] = $_POST['venue_description'] ?? $venue_data_for_form['description'];
        // Giữ ảnh hiện tại nếu có lỗi upload/url mới, trừ khi có ảnh mới được POST
        $venue_data_for_form['image_url'] = (!empty($errors['venue_image_file']) && !empty($errors['venue_image_url'])) ? ($_POST['current_venue_image_url'] ?? $venue_data_for_form['image_url']) : $final_venue_image_url;
        $venue_data_for_form['display_order'] = $_POST['venue_display_order'] ?? $venue_data_for_form['display_order'];
        $venue_data_for_form['is_visible'] = isset($_POST['venue_is_visible']); // Lấy từ POST nếu có
    }
}


// Lấy danh sách venues cho service này
$venues = $serviceDAO->getAllVenuesByServiceIdForAdmin($service_id);

$pageTitle = "Manage Venues for " . htmlspecialchars($parentService['name']);
$formTitle = ($venue_action === 'edit_venue' && $current_venue_id) ? "Edit Venue: " . htmlspecialchars($venue_data_for_form['name']) : "Add New Venue";
$submitButtonText = ($venue_action === 'edit_venue' && $current_venue_id) ? "Update Venue" : "Add Venue";

$title = $pageTitle . " | VINICA Admin";
ob_start();
?>

<div class="container mt-4 admin-management-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard/service-management">Manage Services</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $pageTitle; ?></li>
        </ol>
    </nav>
    <h1 class="mb-4 page-title"><?php echo $pageTitle; ?></h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <?php if (isset($errors['database'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errors['database']); ?></div>
    <?php endif; ?>

    <!-- Form Thêm/Sửa Venue -->
    <div class="card mb-4" id="venue-form-card"> <!-- Thêm ID để có thể scroll tới nếu cần -->
        <div class="card-header">
            <h5 class="mb-0"><i class="fas <?php echo ($venue_action === 'edit_venue' && $current_venue_id) ? 'fa-edit' : 'fa-plus-circle'; ?> me-2"></i><?php echo $formTitle; ?></h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/VINICA/admin-dashboard/service-management/venues?service_id=<?php echo $service_id; ?><?php echo ($venue_action === 'edit_venue' && $current_venue_id) ? '&action=edit&venue_id='.$current_venue_id : ''; ?>" enctype="multipart/form-data">
                <input type="hidden" name="venue_form_action" value="<?php echo $venue_action; ?>"> <!-- 'add_venue' or 'edit_venue' -->
                <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                <?php if ($venue_action === 'edit_venue' && $current_venue_id): ?>
                    <input type="hidden" name="venue_id" value="<?php echo $current_venue_id; ?>">
                    <input type="hidden" name="current_venue_image_url" value="<?php echo htmlspecialchars($venue_data_for_form['image_url']); ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="venue_name" class="form-label"><strong>Venue Name <span class="text-danger">*</span></strong></label>
                        <input type="text" class="form-control <?php echo isset($errors['venue_name']) ? 'is-invalid' : ''; ?>" id="venue_name" name="venue_name" value="<?php echo htmlspecialchars($venue_data_for_form['name']); ?>" required>
                        <?php if (isset($errors['venue_name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['venue_name']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="venue_capacity" class="form-label"><strong>Capacity</strong></label>
                        <input type="text" class="form-control" id="venue_capacity" name="venue_capacity" value="<?php echo htmlspecialchars($venue_data_for_form['capacity']); ?>">
                        <small class="form-text text-muted">E.g., "10-20 guests", "Up to 50 people"</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="venue_description" class="form-label"><strong>Description / Highlights</strong></label>
                    <textarea class="form-control" id="venue_description" name="venue_description" rows="3"><?php echo htmlspecialchars($venue_data_for_form['description']); ?></textarea>
                    <small class="form-text text-muted">Short description or key features of the venue.</small>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-6 mb-3">
                        <label for="venue_image_file" class="form-label"><small>Upload New Image (Optional):</small></label>
                        <input type="file" class="form-control <?php echo isset($errors['venue_image_file']) ? 'is-invalid' : ''; ?>" id="venue_image_file" name="venue_image_file" accept=".jpg,.jpeg,.png,.gif,.webp">
                         <?php if (isset($errors['venue_image_file'])): ?>
                            <div class="invalid-feedback d-block"><?php echo $errors['venue_image_file']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="venue_image_url" class="form-label"><small>Or Enter Image URL:</small></label>
                        <input type="text" class="form-control <?php echo isset($errors['venue_image_url']) ? 'is-invalid' : ''; ?>" id="venue_image_url" name="venue_image_url" value="<?php echo ($venue_action === 'edit_venue' && !isset($_POST['venue_image_url'])) ? htmlspecialchars($venue_data_for_form['image_url']) : (isset($_POST['venue_image_url']) ? htmlspecialchars($_POST['venue_image_url']) : ''); ?>" placeholder="https://example.com/image.jpg or /path/to/image.jpg">
                        <?php if (isset($errors['venue_image_url'])): ?>
                            <div class="invalid-feedback d-block"><?php echo $errors['venue_image_url']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                 <?php if ($venue_action === 'edit_venue' && !empty($venue_data_for_form['image_url'])): ?>
                    <div class="mb-3">
                        <small>Current Image:</small><br>
                        <?php
                        $display_image_url = htmlspecialchars($venue_data_for_form['image_url']);
                        // Kiểm tra nếu là đường dẫn tương đối từ public dir thì thêm base path nếu cần
                        if (strpos($display_image_url, '/') === 0 && !filter_var($display_image_url, FILTER_VALIDATE_URL)) {
                             // Giả định $display_image_url đã có /VINICA/ ở đầu nếu nó là internal path từ config
                        }
                        ?>
                        <img src="<?php echo $display_image_url; ?>" alt="Current Venue Image" style="max-width: 200px; max-height: 150px; object-fit: cover; margin-top: 5px;">
                         <p><small><em>If you upload a new image or provide a new URL, this image will be replaced (and deleted from server if it was an uploaded file).</em></small></p>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="venue_display_order" class="form-label"><strong>Display Order</strong></label>
                        <input type="number" class="form-control" id="venue_display_order" name="venue_display_order" value="<?php echo (int)$venue_data_for_form['display_order']; ?>">
                    </div>
                    <div class="col-md-6 mb-3 align-self-center">
                        <div class="form-check mt-3">
                            <input type="checkbox" class="form-check-input" id="venue_is_visible" name="venue_is_visible" value="1" <?php echo $venue_data_for_form['is_visible'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="venue_is_visible"><strong>Is Visible?</strong></label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?php echo $submitButtonText; ?></button>
                <?php if ($venue_action === 'edit_venue' && $current_venue_id): ?>
                    <a href="/VINICA/admin-dashboard/service-management/venues?service_id=<?php echo $service_id; ?>" class="btn btn-secondary">Cancel Edit & Add New</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <hr class="my-4">

    <h3 class="mb-3">Existing Venues for "<?php echo htmlspecialchars($parentService['name']); ?>"</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Capacity</th>
                    <th>Image</th>
                    <th>Visible</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($venues)): ?>
                    <?php foreach ($venues as $venue): ?>
                        <tr class="<?php echo ($venue_action === 'edit_venue' && $current_venue_id == $venue['id']) ? 'table-info' : ''; ?>">
                            <td><?php echo htmlspecialchars($venue['id']); ?></td>
                            <td><?php echo htmlspecialchars($venue['name']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($venue['capacity'])); ?></td>
                            <td>
                                <?php if (!empty($venue['image_url'])): ?>
                                    <?php
                                    $img_url = htmlspecialchars($venue['image_url']);
                                    // Kiểm tra nếu là đường dẫn tương đối từ public dir thì thêm base path nếu cần
                                    if (strpos($img_url, '/') === 0 && !filter_var($img_url, FILTER_VALIDATE_URL)) {
                                        // Giả định $img_url đã có /VINICA/ nếu là internal path
                                    }
                                    ?>
                                    <img src="<?php echo $img_url; ?>" alt="<?php echo htmlspecialchars($venue['name']); ?>" style="max-width: 100px; max-height: 70px; object-fit: cover;">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($venue['is_visible']): ?>
                                    <span class="badge bg-success">Visible</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Hidden</span>
                                <?php endif; ?>
                                <!-- Toggle button (TODO if needed) -->
                            </td>
                            <td><?php echo htmlspecialchars($venue['display_order']); ?></td>
                            <td>
                                <a href="/VINICA/admin-dashboard/service-management/venues?service_id=<?php echo $service_id; ?>&action=edit&venue_id=<?php echo $venue['id']; ?>#venue-form-card" class="btn btn-sm btn-primary me-1 mb-1" title="Edit Venue">
                                    <i class="bx bxs-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger mb-1" title="Delete Venue" onclick="confirmVenueDelete(<?php echo $venue['id']; ?>, '<?php echo htmlspecialchars(addslashes($venue['name'])); ?>', <?php echo $service_id; ?>)">
                                    <i class="bx bxs-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No venues found for this service yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Venue Confirmation Modal -->
<div class="modal fade" id="deleteVenueModal" tabindex="-1" aria-labelledby="deleteVenueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteVenueModalLabel">Confirm Venue Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the venue: <strong id="venueNameToDelete"></strong>?
                <p class="text-danger small mt-2">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteVenueForm" method="POST" action="/VINICA/admin-dashboard/service-management/venues/delete" style="display: inline;">
                    <input type="hidden" name="venue_id" id="venue_id_to_delete">
                    <input type="hidden" name="service_id_redirect" id="service_id_redirect_for_delete">
                    <button type="submit" class="btn btn-danger">Delete Venue</button>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
require_once __DIR__ . '/../main.php';
?>

<script>
    function confirmVenueDelete(venueId, venueName, serviceIdRedirect) {
        document.getElementById('venueNameToDelete').textContent = venueName;
        document.getElementById('venue_id_to_delete').value = venueId;
        document.getElementById('service_id_redirect_for_delete').value = serviceIdRedirect;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteVenueModal'));
        deleteModal.show();
    }

    // Nếu đang edit, scroll tới form
    <?php if ($venue_action === 'edit_venue' && $current_venue_id): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const formCard = document.getElementById('venue-form-card');
        if (formCard) {
            formCard.scrollIntoView({ behavior: 'smooth' });
        }
    });
    <?php endif; ?>

    // Xử lý hiển thị tên file khi chọn file upload cho venue image
    const venueImageFileInput = document.getElementById('venue_image_file');
    if (venueImageFileInput) {
        venueImageFileInput.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Choose file';
            // Hiển thị tên file (có thể thêm một element để hiển thị nếu muốn)
            // console.log(fileName);
            // Ví dụ: tự động điền vào trường URL nếu rỗng, hoặc xóa trường URL
            // document.getElementById('venue_image_url').value = ''; // Xóa URL nếu chọn file
        });
    }
</script> 