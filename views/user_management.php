<?php
require_once __DIR__ . '/../DAO/userDAO.php'; 
        
$userDAO = new UserDAO(); 
$user = null; 
if (isset($_SESSION['user_id'])) {
    $user = $userDAO->findById($_SESSION['user_id']);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /VINICA/login");
    exit;
} elseif (!$user || $user['role'] !== 'admin') { 
    header("Location: /VINICA/login");
    exit;
}

$title = "User Management | VINICA Admin";
$description = "Manage admin and staff accounts for VINICA.";
$keywords = "VINICA, admin, user management, accounts";

$userDAO = new UserDAO();
$users = $userDAO->getAllUsers(); 

ob_start();
?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">User Management</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>User Management</h1>
        <a href="/VINICA/admin-dashboard/user-management/user-add" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New User
        </a>
    </div>

    <?php if (isset($_SESSION['user_message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['user_message_type']); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['user_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['user_message'], $_SESSION['user_message_type']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            All Users
        </div>
        <div class="card-body">
            <?php if (!empty($users)): ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                                <td>
                                    <a href="/VINICA/admin-dashboard/user-management/user-edit?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="/VINICA/admin-dashboard/user-management/user-delete?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No users found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/main.php'; 
?> 