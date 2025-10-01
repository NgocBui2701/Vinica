<?php
require_once __DIR__ . '/../DAO/userDAO.php';
$content_file = __DIR__ . '/content.json';

$userDAO = new UserDAO();
$user = $userDAO->findById($_SESSION['user_id']);

if (!isset($_SESSION['user_id'])) {
    header("Location: /VINICA/login");
    exit;
} elseif ($user['role'] !== 'admin') {
    header("Location: /VINICA/login");
    exit;
}

$title = "Admin Dashboard | VINICA";
$description = "Manage VINICA's content, reservations, and users.";
$keywords = "VINICA, admin, dashboard, content management";

ob_start();
?>
<div class="container mt-5">
<div class="container d-flex justify-content-end mb-3">
    <a href="/VINICA/logout" class="btn btn-danger mx-2" style = "width: 20%;" >Logout</a>
    <a href="/VINICA/recovery" class="btn btn-primary" style = "width: 20%;" >Change Password</a>
</div>
    <header class="main-header" data-aos="fade-up">
        <h1><i>VINICA Admin Dashboard</i></h1>
        <div class="diamond-separator">
            <span class="diamond"></span><span class="diamond"></span><span class="diamond"></span>
        </div>
    </header>
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">Add, edit, or delete admin and staff accounts.</p>
                    <a href="/VINICA/admin-dashboard/user-management" class="btn btn-custom">Go to User Management</a>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Pages</h5>
                    <p class="card-text">Edit content for Home, About, Services, and Menu pages.</p>
                    <a href="/VINICA/admin-dashboard/page-management" class="btn btn-custom">Go to Page Management</a>
                </div>
            </div>
        </div> -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Home Page Content</h5>
                    <p class="card-text">Edit the content for the main home page.</p>
                    <a href="/VINICA/admin-dashboard/manage_home_content" class="btn btn-custom">Go to Home Content Management</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage About Page Content</h5>
                    <p class="card-text">Edit the content for the About page.</p>
                    <a href="/VINICA/admin-dashboard/manage_about_content" class="btn btn-custom">Go to About Content Management</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Services</h5>
                    <p class="card-text">Edit event services like Birthday Party, Wedding, etc.</p>
                    <a href="/VINICA/admin-dashboard/service-management" class="btn btn-custom">Go to Service Management</a>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Menu Items</h5>
                    <p class="card-text">Add or update menu items and categories.</p>
                    <a href="/VINICA/admin-dashboard/menu-management" class="btn btn-custom">Go to Menu Management</a>
                </div>
            </div>
        </div>-->
        <!-- <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Reservations</h5>
                    <p class="card-text">View and update customer reservations.</p>
                    <a href="/VINICA/admin-dashboard/reservation-management" class="btn btn-custom">Go to Reservation Management</a>
                </div>
            </div>
        </div>  -->
    </div>
</div>
<?php
$content = ob_get_clean();
require 'main.php';
?>