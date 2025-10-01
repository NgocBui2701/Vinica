<?php
require_once __DIR__ . '/../DAO/pages.php';
require_once __DIR__ . '/../DAO/reservationDAO.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $service = $_POST['services'] ?? '';
    $check_in_date = $_POST['check_in_date'] ?? '';
    $area = $_POST['area'] ?? '';

    if (!empty($full_name) && !empty($phone) && !empty($email) && !empty($check_in_date)) {
        $data = [
            'full_name' => $full_name,
            'phone' => $phone,
            'email' => $email,
            'service' => $service,
            'check_in_date' => $check_in_date,
            'area' => $area,
            'status' => 'pending' 
        ];

        $success = insert_reservation($data);

        if ($success) {
            header("Location: /VINICA/reservation");
            echo "<script>alert('Reservation successful!');</script>";
            exit;
        } else {
            echo "Error occurred while processing your reservation. Please try again later.";
        }
    } else {
        echo "Please fill in all required fields.";
    }
}
// Lấy SEO cho trang
$reservationPageDetails = get_page_details_by_slug('reservation');
if ($reservationPageDetails && !empty($reservationPageDetails['title'])) {
    $page_seo_title = htmlspecialchars($reservationPageDetails['title'], ENT_QUOTES, 'UTF-8');
} // Giữ default title nếu không có trong DB hoặc rỗng
if ($reservationPageDetails && !empty($reservationPageDetails['meta_description'])) {
    $page_seo_meta_description = htmlspecialchars($reservationPageDetails['meta_description'], ENT_QUOTES, 'UTF-8');
}
if ($reservationPageDetails && !empty($reservationPageDetails['meta_keywords'])) {
    $page_seo_meta_keywords = htmlspecialchars($reservationPageDetails['meta_keywords'], ENT_QUOTES, 'UTF-8');
}
// Gán giá trị SEO cuối cùng cho layout
$title = $page_seo_title . (strpos($page_seo_title, 'VINICA') === false ? " | VINICA" : "");
$metaDescription = $page_seo_meta_description;
$metaKeywords = $page_seo_meta_keywords;

ob_start();
?>
<div data-aos="fade-up">
        <img src="/VINICA/layout/img/home_1.jpg" alt="Background Image" class="background-image">
    </div>
    <div class="reservation-container">
        <header class="main-header" data-aos="fade-up">
            <h1>Reservation</h1>
            <div class="diamond-separator">
                <span class="diamond"></span><span class="diamond"></span><span class="diamond"></span>
            </div>
        </header>
        <form method="POST" class="reservation-form-wrapper">
            <div class="reservation-form">
                <div class="form-column">
                    <h2 class="column-title">Customer Information</h2>
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" class="form-control-custom" id="fullName" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" class="form-control-custom" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control-custom" id="email" name="email" required>
                    </div>
                </div>

                <div class="form-column">
                    <h2 class="column-title">Order Information</h2>
                    <div class="form-group">
                        <label for="services">Services</label>
                        <input type="text" class="form-control-custom" id="services" name="services" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="checkInDate">Check-in Date</label>
                        <div class="date-input-wrapper">
                            <input type="date" class="form-control-custom" id="checkInDate" name="check_in_date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="area">Area</label>
                        <input type="text" class="form-control-custom" id="area" name="area" placeholder="">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-custom w-5">Confirm</button>
        </form>
    </div>

<?php
$content = ob_get_clean();
require __DIR__ . '/main.php'; 
?>