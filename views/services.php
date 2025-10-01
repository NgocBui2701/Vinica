<?php
require_once __DIR__ . '/../DAO/ServiceDAO.php';
require_once __DIR__ . '/../DAO/pages.php';

$serviceDAO = new ServiceDAO();

$services = []; // Khởi tạo mảng services

// Xác định slug service được yêu cầu (nếu có)
// Biến $service (param từ router) sẽ được extract() trong index.php
$requested_service_slug = (isset($service) && is_string($service) && !empty($service)) ? $service : null;

if ($requested_service_slug) {
    $singleService = $serviceDAO->getVisibleServiceBySlug($requested_service_slug);
    if ($singleService) {
        $services = [$singleService]; // Đặt service tìm thấy vào mảng để vòng lặp hoạt động
        
        // Cập nhật SEO cho service cụ thể
        $page_seo_title = htmlspecialchars($singleService['name']) . " | Event Services";
        $meta_desc_content = strip_tags($singleService['description']); 
        if (mb_strlen($meta_desc_content) > 155) {
            $meta_desc_content = mb_substr($meta_desc_content, 0, 152, 'UTF-8') . '...';
        }
        $page_seo_meta_description = htmlspecialchars($meta_desc_content);
        // Lấy keywords mặc định và thêm tên service, đảm bảo không quá dài
        $base_keywords = "VINICA, event services, function rooms"; // Keywords cơ bản cho một service
        $page_seo_meta_keywords = htmlspecialchars($singleService['name']) . ", " . $base_keywords;

    } else {
        // Service slug không hợp lệ hoặc service không visible
        $page_seo_title = "Service Not Found";
        // $services vẫn rỗng, sẽ hiển thị thông báo "no services available"
        // Ghi đè meta description và keywords cho trang "not found"
        $page_seo_meta_description = "The specific event service you were looking for was not found at VINICA. Please browse our available services.";
        $page_seo_meta_keywords = "VINICA, service not found, event services";
    }
} else {
    // Không có service slug cụ thể -> trang /services chung
    $services = $serviceDAO->getAllVisibleServices();
    
    // Lấy SEO cho trang /services từ bảng 'pages' nếu có
    $servicesPageDetails = get_page_details_by_slug('services');
    if ($servicesPageDetails && !empty($servicesPageDetails['title'])) {
        $page_seo_title = htmlspecialchars($servicesPageDetails['title'], ENT_QUOTES, 'UTF-8');
    } // Giữ default title nếu không có trong DB hoặc rỗng
    if ($servicesPageDetails && !empty($servicesPageDetails['meta_description'])) {
        $page_seo_meta_description = htmlspecialchars($servicesPageDetails['meta_description'], ENT_QUOTES, 'UTF-8');
    }
    if ($servicesPageDetails && !empty($servicesPageDetails['meta_keywords'])) {
        $page_seo_meta_keywords = htmlspecialchars($servicesPageDetails['meta_keywords'], ENT_QUOTES, 'UTF-8');
    }
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
    <header class="main-header" data-aos="fade-up">
        <h1>Explore Our Event Venues</h1>
        <div class="diamond-separator">
            <span class="diamond"></span><span class="diamond"></span><span class="diamond"></span>
        </div>
    </header>
    <main class="services-page-container">
        <div class="page-wrapper">
        <?php if (empty($services)): ?>
            <p class="text-center">Currently, there are no services available. Please check back later.</p>
        <?php else: ?>
            <?php foreach ($services as $index => $service): ?>
                <?php 
                $venues = $serviceDAO->getVisibleVenuesByServiceId($service['id']);
                // Xác định layout dựa trên chỉ số (chẵn/lẻ) để xen kẽ
                $layoutClass = ($index % 2 == 0) ? 'service-section layout-image-left' : 'service-section layout-image-right';
                $imageSlideDirection = ($index % 2 == 0) ? 'slide-right' : 'slide-left';
                $textSlideDirection = ($index % 2 == 0) ? 'slide-left' : 'slide-right';
                ?>
                <!-- SERVICE: <?php echo htmlspecialchars(strtoupper($service['name'])); ?> -->
                <section id="service-<?php echo htmlspecialchars($service['slug']); ?>" class="service-section <?php echo $layoutClass; ?>" data-aos="fade-up">
                    <div class="service-image-wrapper" data-aos="<?php echo $imageSlideDirection; ?>" data-aos-delay="100">
                        <?php if (!empty($service['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?> at VINICA">
                        <?php else: ?>
                            <img src="/VINICA/layout/img/default_service_image.jpg" alt="Default service image for <?php echo htmlspecialchars($service['name']); ?>">
                        <?php endif; ?>
                </div>
                    <div class="service-text-content" data-aos="<?php echo $textSlideDirection; ?>" data-aos-delay="200">
                        <h3 class="service-title"><?php echo htmlspecialchars($service['name']); ?></h3>
                        <?php 
                        // Hiển thị description của service, giả sử nó chứa HTML
                        // Cần đảm bảo HTML này an toàn hoặc được làm sạch nếu người dùng nhập tự do
                        echo $service['description']; 
                        ?>
                </div>
            </section>

                <?php if (!empty($venues)): ?>
                <!-- ======================= <?php echo htmlspecialchars(strtoupper($service['name'])); ?> SPACES ======================= -->
                <section id="listing-<?php echo htmlspecialchars($service['slug']); ?>" class="service-category-listing" data-aos="fade-up">
                <header class="category-listing-header">
                        <h2>Spaces for <?php echo htmlspecialchars($service['name']); ?></h2>
                        <!-- Bạn có thể thêm một mô tả ngắn cho phần listing này nếu muốn, có thể lấy từ DB hoặc để trống -->
                        <p>Discover our curated selection of venues perfect for your <?php echo strtolower(htmlspecialchars($service['name'])); ?>.</p>
                </header>
                    <div class="space-summary-grid initially-hidden"> <!-- giữ lại class initially-hidden nếu JS của bạn cần nó -->
                        <?php foreach ($venues as $venue): ?>
                        <article class="space-summary-card" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                            <a href="#" class="card-link-wrapper"> <!-- Thay đổi href nếu cần link đến chi tiết venue -->
                            <div class="card-image-wrapper">
                                    <?php if (!empty($venue['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($venue['image_url']); ?>" alt="<?php echo htmlspecialchars($venue['name']); ?>">
                                    <?php else: ?>
                                        <img src="/VINICA/layout/img/default_venue_image.jpg" alt="Default image for <?php echo htmlspecialchars($venue['name']); ?>">
                                    <?php endif; ?>
                            </div>
                            <div class="card-content">
                                    <h3 class="card-title"><?php echo htmlspecialchars($venue['name']); ?></h3>
                                    <?php if (!empty($venue['capacity'])): ?>
                                    <p class="card-meta"><i class='bx bxs-group'></i> Capacity: <?php echo htmlspecialchars($venue['capacity']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($venue['description'])): // Giả sử 'description' của venue là 'highlights'
                                          // Nếu 'description' của venue có thể chứa HTML, bạn sẽ echo trực tiếp
                                          // Nếu là plain text, dùng nl2br(htmlspecialchars(...)) nếu muốn xuống dòng
                                    ?>     
                                    <p class="card-highlights"><?php echo nl2br(htmlspecialchars($venue['description'])); // Sử dụng htmlspecialchars và nl2br nếu là plain text và muốn giữ xuống dòng ?></p>
                                    <?php endif; ?>
                            </div>
                        </a>
                    </article>
                        <?php endforeach; ?>
                </div>
                <div class="show-all-button-container">
                        <button class="btn btn-custom-outline show-all-btn">Show All Spaces for <?php echo htmlspecialchars($service['name']); ?> <i class='bx bx-chevron-down'></i></button>
                </div>
            </section>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

            <section class="main-header final" data-aos="fade-up">
                <h3>Ready to Plan Your Event?</h3>
                <p>Let our experienced team help you create a truly exceptional and memorable occasion. Contact us today to discuss your vision and requirements.</p>
            <a href="/VINICA/contact" class="btn btn-custom btn-lg">Contact Us Now</a> 
            </section>
        </div>
    </main>

<?php
$content = ob_get_clean();
require __DIR__ . '/main.php'; 
?>