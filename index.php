<?php 
    session_start();
    // if (!isset($_SESSION['user'])) {
    //     header('Location: login.php');
    //     exit;
    // }
    include 'view/header.php';
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    switch ($page) {
        case 'home':
            include 'view/home.php';
            break;
        case 'about':
            include 'view/about.php';
            break;
        case 'service':
            include 'view/service.php';
            break;
        case 'menu':
            include 'view/menu.php';
            break;
        case 'booking':
            include 'view/booking.php';
            break; 
        case 'contact':
            include 'view/contact.php';
            break;
        default:
            echo "<div class='container mt-5'><h4>404 - Trang không tồn tại</h4></div>";
            break;
    }
    include 'view/footer.php';
?>