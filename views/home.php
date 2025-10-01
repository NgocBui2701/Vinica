<?php
// Nạp các DAO cần thiết
require_once __DIR__ . '/../DAO/PageContentDAO.php';
require_once __DIR__ . '/../DAO/MenuDAO.php';
require_once __DIR__ . '/../DAO/pages.php'; 

// Khởi tạo DAO
$pageContentDAO = new PageContentDAO();
$menuDAO = new MenuDAO();
// Lấy nội dung động cho trang chủ từ CSDL (bảng page_content)
$homeContent = [];
$contentItems = $pageContentDAO->getContentByPageSlug('home', true);
// Chuyển đổi mảng contentItems thành một mảng dễ sử dụng hơn, lấy giá trị đầu tiên nếu section chỉ có 1 item
foreach ($contentItems as $key => $items) {
    if (count($items) === 1) {
        $homeContent[$key] = $items[0]; // Lấy item duy nhất
    } else {
        $homeContent[$key] = $items; // Giữ nguyên mảng nếu có nhiều items (ít dùng cho trang này)
    }
}

// Hàm trợ giúp để lấy giá trị nội dung an toàn
function getContentValue($contentArray, $key, $field = 'text', $default = '') {
    if (isset($contentArray[$key]) && isset($contentArray[$key][$field])) {
        return htmlspecialchars($contentArray[$key][$field], ENT_QUOTES, 'UTF-8');
    }
    return $default;
}

// Hàm trợ giúp để lấy nội dung HTML thô
function getRawHtmlContent($contentArray, $key, $field = 'text', $default = '') {
    if (isset($contentArray[$key]) && isset($contentArray[$key][$field])) {
        return $contentArray[$key][$field]; // Trả về giá trị thô, không escape
    }
    return $default;
}

// Lấy danh sách các món ăn đặc trưng
$signatureDishes = $menuDAO->getSignatureDishes(6); // Lấy 6 món

// Lấy chi tiết trang (bao gồm meta tags) từ bảng 'pages' cho trang 'home'
$pageDetails = get_page_details_by_slug('home');

// Các biến meta cho trang, lấy từ $pageDetails hoặc giá trị mặc định
$title = $pageDetails && !empty($pageDetails['title']) ? htmlspecialchars($pageDetails['title'], ENT_QUOTES, 'UTF-8') : "Welcome to VINICA";
$metaDescription = $pageDetails && !empty($pageDetails['meta_description']) ? htmlspecialchars($pageDetails['meta_description'], ENT_QUOTES, 'UTF-8') : "Discover VINICA";
$metaKeywords = $pageDetails && !empty($pageDetails['meta_keywords']) ? htmlspecialchars($pageDetails['meta_keywords'], ENT_QUOTES, 'UTF-8') : "VINICA, fine dining, European cuisine, restaurant, elegant dining, special occasions";

ob_start();

// Helper function to check if a section has any visible content
function hasVisibleContent($contentArray, $keys, $field = 'text') {
    foreach ((array)$keys as $key) {
        if (!empty($contentArray[$key][$field])) {
            return true;
        }
    }
    return false;
}

// Define content keys for each major section
$heroSectionKeys = ['hero_image_url'];
$introSectionKeys = [
    'intro_main_heading', 'art_dining_heading', 'art_dining_text', 'art_dining_image_url',
    'events_heading', 'events_text', 'events_image_url',
    'diversity_heading', 'diversity_text', 'diversity_menu_link_href', 'diversity_menu_link_text', 'diversity_image_url'
];
$signatureDishesSectionKeys = ['signature_dishes_heading']; // Button text/links also checked
$ourStorySectionKeys = ['our_story_image_url', 'our_story_heading', 'our_story_text', 'our_story_learn_more_href', 'our_story_learn_more_text'];

?>
<?php if (hasVisibleContent($homeContent, $heroSectionKeys)): ?>
    <div data-aos="fade-up">
        <img src="<?php echo getContentValue($homeContent, 'hero_image_url', 'text', '/VINICA/layout/img/home_1.jpg'); ?>" alt="Background Image of VINICA Restaurant" class="background-image">
    </div>
<?php endif; ?>

<?php
// Check for intro section content
$showIntroSection = hasVisibleContent($homeContent, 'intro_main_heading') ||
                    hasVisibleContent($homeContent, ['art_dining_heading', 'art_dining_text', 'art_dining_image_url']) ||
                    hasVisibleContent($homeContent, ['events_heading', 'events_text', 'events_image_url']) ||
                    hasVisibleContent($homeContent, ['diversity_heading', 'diversity_text', 'diversity_menu_link_href', 'diversity_menu_link_text', 'diversity_image_url']);
?>
<?php if ($showIntroSection): ?>
    <!-- SECTION 1: RESTAURANT INTRODUCTION -->
    <div class="page-wrapper">
        <section class="restaurant-container">
            <?php if (getContentValue($homeContent, 'intro_main_heading')): ?>
            <header class="main-header" data-aos="fade-up">
                <h1><i><?php echo getContentValue($homeContent, 'intro_main_heading', 'text', 'A Taste of Europe, A Touch of Elegance'); ?></i></h1>
                <div class="diamond-separator">
                    <span class="diamond"></span><span class="diamond"></span><span class="diamond"></span>
                </div>
            </header>
            <?php endif; ?>

            <?php
            $showArtDining = hasVisibleContent($homeContent, 'art_dining_heading') ||
                             hasVisibleContent($homeContent, 'art_dining_text', 'text') || // Check 'text' for TinyMCE
                             hasVisibleContent($homeContent, 'art_dining_image_url');
            ?>
            <?php if ($showArtDining): ?>
            <div class="content">
                <header class="main-header-2 right" data-aos="slide-right">
                    <h2>
                        <span class="heading-diamond-icon-container right large">
                            <span class="diamond-icon-wrapper">
                                <span class="d-s d1"></span><span class="d-s d2"></span>
                                <span class="d-s d3"></span><span class="d-s d4"></span>
                            </span>
                        </span>
                        <?php echo getContentValue($homeContent, 'art_dining_heading', 'text', 'The Art of Fine Dining'); ?>
                    </h2>
                </header>
                <article class="info-content-block">
                    <?php if (getRawHtmlContent($homeContent, 'art_dining_text')): ?>
                    <div class="text-column" data-aos="slide-right">
                        <?php echo getRawHtmlContent($homeContent, 'art_dining_text', 'text', '<p> </p>'); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (getContentValue($homeContent, 'art_dining_image_url')): ?>
                    <div class="image-column" data-aos="slide-left">
                        <img src="<?php echo getContentValue($homeContent, 'art_dining_image_url', 'text', 'https://shbmastercardworld.com.vn/wp-content/uploads/2024/02/img-resize-1-1.png'); ?>" alt="fine-dining">
                    </div>
                    <?php endif; ?>
                </article>
            </div>
            <?php endif; ?>

            <?php
            $showEvents = hasVisibleContent($homeContent, 'events_heading') ||
                          hasVisibleContent($homeContent, 'events_text', 'text') ||
                          hasVisibleContent($homeContent, 'events_image_url');
            ?>
            <?php if ($showEvents): ?>
            <div class="content">
                <header class="main-header-2 left" data-aos="slide-left">
                    <h2><?php echo getContentValue($homeContent, 'events_heading', 'text', 'Private Events & Celebrations'); ?>
                        <span class="heading-diamond-icon-container right large">
                            <span class="diamond-icon-wrapper">
                                <span class="d-s d1"></span><span class="d-s d2"></span>
                                <span class="d-s d3"></span><span class="d-s d4"></span>
                            </span>
                        </span>
                    </h2>
                </header>
                <article class="info-content-block">
                    <?php if (getContentValue($homeContent, 'events_image_url')): ?>
                    <div class="image-column" data-aos="slide-right">
                        <img src="<?php echo getContentValue($homeContent, 'events_image_url', 'text', 'https://shbmastercardworld.com.vn/wp-content/uploads/2024/08/15-scaled.jpg'); ?>" alt="Lounge & Bar">
                    </div>
                    <?php endif; ?>
                    <?php if (getRawHtmlContent($homeContent, 'events_text')): ?>
                    <div class="text-column" data-aos="slide-left">
                        <?php echo getRawHtmlContent($homeContent, 'events_text', 'text', '<p></p>'); ?>
                    </div>
                    <?php endif; ?>
                </article>
            </div>
            <?php endif; ?>

            <?php
            $showDiversity = hasVisibleContent($homeContent, 'diversity_heading') ||
                             hasVisibleContent($homeContent, 'diversity_text', 'text') ||
                             (hasVisibleContent($homeContent, 'diversity_menu_link_href') && hasVisibleContent($homeContent, 'diversity_menu_link_text')) ||
                             hasVisibleContent($homeContent, 'diversity_image_url');
            ?>
            <?php if ($showDiversity): ?>
            <div class="content">
                <header class="main-header-2 right" data-aos="slide-right">
                    <h2>
                        <span class="heading-diamond-icon-container right large">
                            <span class="diamond-icon-wrapper">
                                <span class="d-s d1"></span><span class="d-s d2"></span>
                                <span class="d-s d3"></span><span class="d-s d4"></span>
                            </span>
                        </span>
                        <?php echo getContentValue($homeContent, 'diversity_heading', 'text', 'Diversity In Choices'); ?>
                    </h2>
                </header>
                <article class="info-content-block">
                    <?php if (getRawHtmlContent($homeContent, 'diversity_text') || (getContentValue($homeContent, 'diversity_menu_link_href') && getContentValue($homeContent, 'diversity_menu_link_text'))): ?>
                     <div class="text-column" data-aos="slide-right">
                        <?php echo getRawHtmlContent($homeContent, 'diversity_text', 'text', '<p> </p>'); ?>
                        <?php if (getContentValue($homeContent, 'diversity_menu_link_href') && getContentValue($homeContent, 'diversity_menu_link_text')): ?>
                        <a href="<?php echo getContentValue($homeContent, 'diversity_menu_link_href', 'text', '/VINICA/menu'); ?>" class="details-link"><?php echo getContentValue($homeContent, 'diversity_menu_link_text', 'text', 'Check Menu'); ?></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (getContentValue($homeContent, 'diversity_image_url')): ?>
                    <div class="image-column" data-aos="slide-left">
                        <img src="<?php echo getContentValue($homeContent, 'diversity_image_url', 'text', 'https://shbmastercardworld.com.vn/wp-content/uploads/2024/02/banner-detail-2.png'); ?>" alt="Menu choices">
                    </div>
                    <?php endif; ?>
                </article>
            </div>
            <?php endif; ?>
        </section>
    </div>
<?php endif; ?>

<?php
// Check for signature dishes section content
$showSignatureDishesSection = hasVisibleContent($homeContent, 'signature_dishes_heading') ||
                                !empty($signatureDishes) || // Check if there are actual dishes
                                (hasVisibleContent($homeContent, 'signature_dishes_view_all_href') && hasVisibleContent($homeContent, 'signature_dishes_view_all_text'));
?>
<?php if ($showSignatureDishesSection): ?>
    <!-- SECTION 2: SIGNATURE DISHES -->
    <section class="signature-dishes-section" data-aos="fade-up">
        <?php if (getContentValue($homeContent, 'signature_dishes_heading')): ?>
        <header class="main-header">
            <h2><?php echo getContentValue($homeContent, 'signature_dishes_heading', 'text', 'Our Signature Dishes'); ?></h2>
            <div class="diamond-separator">
                <span class="diamond"></span><span class="diamond"></span><span class="diamond"></span>
            </div>
        </header>
        <?php endif; ?>
        <?php if (!empty($signatureDishes)): ?>
        <div class="signature-dishes-grid">
            <?php foreach ($signatureDishes as $index => $dish): ?>
                    <div class="dish-item" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                        <div class="dish-image-wrapper">
                            <img src="<?php echo htmlspecialchars($dish['image_url'] ?? '/VINICA/layout/img/default_dish.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($dish['name'] ?? 'Signature Dish', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($dish['name'] ?? 'Delicious Dish', ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($dish['description'] ?? 'A culinary delight.', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endforeach; ?>
        </div>
        <?php elseif (getContentValue($homeContent, 'signature_dishes_heading')): // Show message only if header was shown ?>
            <p class="text-center">Signature dishes coming soon!</p>
        <?php endif; ?>
        <?php if (getContentValue($homeContent, 'signature_dishes_view_all_href') && getContentValue($homeContent, 'signature_dishes_view_all_text')): ?>
        <div class="text-center mt-4">
            <a href="<?php echo getContentValue($homeContent, 'signature_dishes_view_all_href', 'text', '/VINICA/menu'); ?>" class="btn btn-custom" role="button" data-aos="fade-up"><?php echo getContentValue($homeContent, 'signature_dishes_view_all_text', 'text', 'View Full Menu'); ?></a>
        </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php
// Check for Our Story section content
$showOurStorySection = hasVisibleContent($homeContent, 'our_story_image_url') ||
                       hasVisibleContent($homeContent, 'our_story_heading') ||
                       hasVisibleContent($homeContent, 'our_story_text', 'text') || // Check 'text' for TinyMCE
                       (hasVisibleContent($homeContent, 'our_story_learn_more_href') && hasVisibleContent($homeContent, 'our_story_learn_more_text'));
?>
<?php if ($showOurStorySection): ?>
    <!-- SECTION 3: OUR STORY (ABOUT US) -->
    <article id="about-our-story" class="about-section-item" data-aos="fade-up">
        <?php if (getContentValue($homeContent, 'our_story_image_url')): ?>
        <div class="about-image-wrapper" data-aos="fade-up-right" data-aos-delay="200">
            <img src="<?php echo getContentValue($homeContent, 'our_story_image_url', 'text', 'https://media.istockphoto.com/id/153743372/photo/restaurant-table-and-chairs-with-place-settings1.jpg?s=612x612&w=0&k=20&c=jtyCnk3Rx3W1a5RZovPyO1k3BjTvFtCcp1RJzk1_m_k='); ?>" alt="Inspiration behind VINICA's story">
        </div>
        <?php endif; ?>
        <?php if (getContentValue($homeContent, 'our_story_heading') || getRawHtmlContent($homeContent, 'our_story_text') || (getContentValue($homeContent, 'our_story_learn_more_href') && getContentValue($homeContent, 'our_story_learn_more_text'))): ?>
        <div class="about-text-content" data-aos="fade-left" data-aos-delay="300">
            <?php if (getContentValue($homeContent, 'our_story_heading')): ?>
            <h2 class="about-section-title"><?php echo getContentValue($homeContent, 'our_story_heading', 'text', 'Our Story'); ?></h2>
            <div class="title-separator-small"></div>
            <?php endif; ?>
            <?php if (getRawHtmlContent($homeContent, 'our_story_text')): ?>
            <?php echo getRawHtmlContent($homeContent, 'our_story_text', 'text', '<p>Founded with a deep passion for culinary excellence and a dream to bring authentic European fine dining to our city, VINICA began its journey. We believe in creating more than just meals; we craft experiences that linger in memory, blending traditional recipes with innovative techniques.</p><p>Our commitment to quality, from sourcing the freshest local ingredients to providing impeccable service, stands at the heart of everything we do. Join us at VINICA, where every dish tells a story of heritage and every visit is a celebration.</p>'); ?>
            <?php endif; ?>
            <?php if (getContentValue($homeContent, 'our_story_learn_more_href') && getContentValue($homeContent, 'our_story_learn_more_text')): ?>
            <a href="<?php echo getContentValue($homeContent, 'our_story_learn_more_href', 'text', '/VINICA/about'); ?>" class="btn btn-custom" data-aos="fade-up"><?php echo getContentValue($homeContent, 'our_story_learn_more_text', 'text', 'Learn More About Us'); ?></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </article>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/main.php'; // Đảm bảo đường dẫn này đúng
?>