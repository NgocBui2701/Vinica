<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include cấu hình
require_once __DIR__ . '/DAO/config.php';

// Định nghĩa các route
$routes = [
    '' => ['view' => 'views/home.php', 'params' => []],
    'home' => ['view' => 'views/home.php', 'params' => []],
    'about' => ['view' => 'views/about.php', 'params' => []],
    'about/our-story' => ['view' => 'views/about.php', 'params' => ['about' => 'story']],
    'about/our-ambience' => ['view' => 'views/about.php', 'params' => ['about' => 'ambience']],
    'about/our-team' => ['view' => 'views/about.php', 'params' => ['about' => 'team']],
    'services' => ['view' => 'views/services.php', 'params' => []],
    'services/birthday-party' => ['view' => 'views/services.php', 'params' => ['service' => 'birthday-party']],
    'services/corporate-event' => ['view' => 'views/services.php', 'params' => ['service' => 'corporate-event']],
    'services/year-end-party' => ['view' => 'views/services.php', 'params' => ['service' => 'year-end-party']],
    'services/wedding' => ['view' => 'views/services.php', 'params' => ['service' => 'wedding']],
    'services/private-dining' => ['view' => 'views/services.php', 'params' => ['service' => 'private-dining']],
    'menu' => ['view' => 'views/menu.php', 'params' => []],
    'menu/lunch-set' => ['view' => 'views/menu.php', 'params' => ['menu' => 'lunch-set']],
    'menu/dinner-set' => ['view' => 'views/menu.php', 'params' => ['menu' => 'dinner-set']],
    'menu/a-la-carte' => ['view' => 'views/menu.php', 'params' => ['menu' => 'a-la-carte']],
    'menu/party-menu' => ['view' => 'views/menu.php', 'params' => ['menu' => 'party-menu']],
    'menu/buffet' => ['view' => 'views/menu.php', 'params' => ['menu' => 'buffet']],
    'reservation' => ['view' => 'views/reservation.php', 'params' => []],

    'login' => ['view' => 'views/login.php', 'params' => []],
    'register' => ['view' => 'views/register.php', 'params' => []],
    'recovery' => ['view' => 'views/recovery.php', 'params' => []],
    'verify' => ['view' => 'views/verify.php', 'params' => []],
    'reset' => ['view' => 'views/reset.php', 'params' => []],
    'change_password' => ['view' => 'views/change_password.php', 'params' => []],
    'logout' => ['view' => 'views/logout.php', 'params' => []],
    'admin-dashboard' => ['view' => 'views/admin_dashboard.php', 'params' => []],
    'admin-dashboard/user-management' => ['view' => 'views/user_management.php', 'params' => []],
    'admin-dashboard/user-management/user-add' => ['view' => 'views/user_add.php', 'params' => []],
    'admin-dashboard/user-management/user-edit' => ['view' => 'views/user_edit.php', 'params' => []],
    'admin-dashboard/user-management/user-delete' => ['view' => 'views/user_delete.php', 'params' => []],
    'admin-dashboard/manage_home_content' => ['view' => 'views/admin/manage_home_content.php', 'params' => []],
    'admin-dashboard/manage_about_content' => ['view' => 'views/admin/manage_about_content.php', 'params' => []],
    'admin-dashboard/service-management' => ['view' => 'views/admin/service_management.php', 'params' => []],
    'admin-dashboard/service-management/add' => ['view' => 'views/admin/service_form.php', 'params' => ['action' => 'add']],
    'admin-dashboard/service-management/edit' => ['view' => 'views/admin/service_form.php', 'params' => ['action' => 'edit']],
    'admin-dashboard/service-management/delete' => ['view' => 'views/admin/service_actions.php', 'params' => ['action_type' => 'delete_service']],
    'admin-dashboard/service-management/venues' => ['view' => 'views/admin/venue_management.php', 'params' => []],
    'admin-dashboard/service-management/venues/delete' => ['view' => 'views/admin/venue_actions.php', 'params' => ['action_type' => 'delete_venue']],
    'admin-dashboard/service-management/venues/add' => ['view' => 'views/admin/venue_form.php', 'params' => ['action' => 'add']],
    'admin-dashboard/service-management/venues/edit' => ['view' => 'views/admin/venue_form.php', 'params' => ['action' => 'edit']],
    'admin-dashboard/reservation-management' => ['view' => 'views/admin/reservation_management.php', 'params' => []],
];

// Lấy URL từ request
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

// Xử lý route
if (array_key_exists($url, $routes)) {
    $route = $routes[$url];
    $view = $route['view'];
    $params = $route['params'];

   // Truyền tham số vào file view
    if (!empty($params)) {
        extract($params);
    }

    if (file_exists($view)) {
        require_once $view;
    } else {
        http_response_code(500);
        echo "Lỗi: Không tìm thấy tệp view: " . htmlspecialchars($view);
    }

} else {
    http_response_code(404);
    echo "404 - Not Found";
}
?>