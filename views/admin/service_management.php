<?php
require_once __DIR__ . '/../../DAO/UserDAO.php';
require_once __DIR__ . '/../../DAO/ServiceDAO.php';
require_once __DIR__ . '/../../DAO/pdo.php'; // ServiceDAO uses pdo_query etc.

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
    header("Location: /VINICA/admin-dashboard"); // Hoặc trang không có quyền truy cập
    exit;
}

$serviceDAO = new ServiceDAO();
$services = $serviceDAO->getAllServicesForAdmin(); // Lấy tất cả dịch vụ cho admin

$title = "Manage Services | VINICA Admin";
ob_start();
?>

<div class="container mt-4 admin-management-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Services</li>
        </ol>
    </nav>
    <h1 class="mb-4 page-title">Manage Services</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['info_message'])): ?>
        <div class="alert alert-info"><?php echo $_SESSION['info_message']; unset($_SESSION['info_message']); ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="/VINICA/admin-dashboard/service-management/add" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Service
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Image</th>
                    <th>Visible</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['id']); ?></td>
                            <td><?php echo htmlspecialchars($service['name']); ?></td>
                            <td><?php echo htmlspecialchars($service['slug']); ?></td>
                            <td>
                                <?php if (!empty($service['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" style="max-width: 100px; max-height: 70px; object-fit: cover;">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($service['is_visible']): ?>
                                    <span class="badge bg-success">Visible</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Hidden</span>
                                <?php endif; ?>
                                <!-- Toggle button will go here -->
                            </td>
                            <td><?php echo htmlspecialchars($service['display_order']); ?></td>
                            <td>
                                <a href="/VINICA/admin-dashboard/service-management/edit?id=<?php echo $service['id']; ?>" class="btn btn-sm btn-primary me-1" title="Edit Service">
                                    <i class="bx bxs-edit"></i>
                                </a>
                                <a href="/VINICA/admin-dashboard/service-management/venues?service_id=<?php echo $service['id']; ?>" class="btn btn-sm btn-info me-1" title="Manage Venues">
                                    <i class="bx bx-store"></i> <span class="d-none d-md-inline">Venues</span>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Delete Service" onclick="confirmDelete(<?php echo $service['id']; ?>, '<?php echo htmlspecialchars(addslashes($service['name'])); ?>')">
                                    <i class="bx bxs-trash"></i>
                                </button>
                                <!-- Form for delete will be added via JS -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No services found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal (Bootstrap 5) -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the service: <strong id="serviceNameToDelete"></strong>?<br>
        This action will also delete all associated venues and cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteServiceForm" method="POST" action="/VINICA/admin-dashboard/service-management/delete" style="display: inline;">
            <input type="hidden" name="service_id" id="serviceIdToDelete" value="">
            <input type="hidden" name="action" value="delete_service">
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function confirmDelete(serviceId, serviceName) {
    document.getElementById('serviceIdToDelete').value = serviceId;
    document.getElementById('serviceNameToDelete').textContent = serviceName;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
}
</script>

<?php
$content = ob_get_clean();
// Giả sử bạn có một file layout chính cho admin tên là 'admin_main.php' hoặc tương tự
// và nó nằm trong thư mục 'views/admin/' hoặc 'views/layout/'
// Nếu không có file layout admin riêng, bạn có thể include main.php và điều chỉnh nếu cần
// Ví dụ: require __DIR__ . '/../main.php';
// Hoặc:
if (file_exists(__DIR__ . '/../main.php')) { // Kiểm tra file main.php có tồn tại không
    require __DIR__ . '/../main.php';
} elseif (file_exists(__DIR__ . '/../../views/main.php')) {
    require __DIR__ . '/../../views/main.php';
} else {
    echo "Admin layout file not found.";
}
?> 