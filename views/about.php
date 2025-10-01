<?php
// Nạp các DAO cần thiết
require_once __DIR__ . '/../DAO/PageContentDAO.php';
require_once __DIR__ . '/../DAO/pages.php';

// Khởi tạo DAO
$pageContentDAO = new PageContentDAO();
$about = isset($about) ? $about : 'all';

// Lấy nội dung động cho trang giới thiệu từ CSDL (bảng page_content)
$aboutContent = [];
$contentItems = $pageContentDAO->getContentByPageSlug('about', true); // Chỉ lấy mục hiển thị

// Chuyển đổi mảng contentItems thành một mảng dễ sử dụng hơn
foreach ($contentItems as $key => $items) {
    if (count($items) === 1) {
        $aboutContent[$key] = $items[0]; // Lấy item duy nhất
    } else {
        // Trường hợp này ít có khả năng xảy ra với cấu trúc hiện tại (1 item_order cho mỗi section_key)
        $aboutContent[$key] = $items; 
    }
}

// Lấy chi tiết trang (bao gồm meta tags) từ bảng 'pages' cho trang 'about'
$pageDetails = get_page_details_by_slug('about');

// Các biến meta cho trang, lấy từ $pageDetails hoặc giá trị mặc định
$title = $pageDetails && !empty($pageDetails['title']) ? htmlspecialchars($pageDetails['title'], ENT_QUOTES, 'UTF-8') : "About VINICA";
$metaDescription = $pageDetails && !empty($pageDetails['meta_description']) ? htmlspecialchars($pageDetails['meta_description'], ENT_QUOTES, 'UTF-8') : "Learn more about VINICA.";
$metaKeywords = $pageDetails && !empty($pageDetails['meta_keywords']) ? htmlspecialchars($pageDetails['meta_keywords'], ENT_QUOTES, 'UTF-8') : "VINICA, about us, restaurant story";

// Hàm trợ giúp để lấy giá trị nội dung an toàn (tương tự home.php)
function getContentValue($contentArray, $key, $field = 'text', $default = '') {
    if (isset($contentArray[$key]) && isset($contentArray[$key][$field]) && !empty($contentArray[$key][$field])) {
        return htmlspecialchars($contentArray[$key][$field], ENT_QUOTES, 'UTF-8');
    }
    return $default;
}

// Hàm trợ giúp để lấy nội dung HTML thô (tương tự home.php)
function getRawHtmlContent($contentArray, $key, $field = 'text', $default = '') {
    if (isset($contentArray[$key]) && isset($contentArray[$key][$field]) && !empty($contentArray[$key][$field])) {
        return $contentArray[$key][$field]; // Trả về giá trị thô, không escape
    }
    return $default;
}

// Hàm kiểm tra xem một section có nội dung để hiển thị không
function hasAnyContent($contentArray, $keys, $checkType = 'any_text_or_html') {
    foreach ((array)$keys as $key) {
        if ($checkType === 'image_url' && !empty(getContentValue($contentArray, $key))) {
            return true;
        }
        if (($checkType === 'any_text_or_html') && (!empty(getContentValue($contentArray, $key)) || !empty(getRawHtmlContent($contentArray, $key)))) {
            return true;
        }
    }
    return false;
}

ob_start();
?>

<?php 
$heroImageUrl = getContentValue($aboutContent, 'about_hero_image_url', 'text', '/VINICA/layout/img/default_hero_about.jpg');
$showHeroImage = !empty($heroImageUrl) && $heroImageUrl !== '/VINICA/layout/img/default_hero_about.jpg'; // Chỉ hiển thị nếu có ảnh cụ thể
?>
<?php if ($showHeroImage): ?>
<div data-aos="fade-up">
    <img src="<?php echo $heroImageUrl; ?>" alt="Background Image of VINICA Restaurant" class="background-image">
</div> 
<?php endif; ?>

<?php
$showPageHeader = hasAnyContent($aboutContent, ['about_page_main_heading', 'about_page_subtitle']);
?>
<?php if ($showPageHeader): ?>
<div class="page-wrapper">
    <section class="restaurant-container">
        <header class="main-header" data-aos="fade-up">
            <?php if (getContentValue($aboutContent, 'about_page_main_heading')): ?>
            <h1><?php echo getContentValue($aboutContent, 'about_page_main_heading', 'text', 'In Our Restaurant'); ?></h1>
            <div class="diamond-separator">
                <span class="diamond"></span><span class="diamond"></span><span class="diamond"></span>
            </div>
            <?php endif; ?>
            <?php if (getRawHtmlContent($aboutContent, 'about_page_subtitle')): ?>
            <?php echo getRawHtmlContent($aboutContent, 'about_page_subtitle', 'text', '<p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">More than just a restaurant, Vinica is a celebration of European culinary artistry and heartfelt hospitality.</p>'); ?>
            <?php endif; ?>
        </header>
    </section>
</div>
<?php endif; ?>

<?php 
$showStorySection = hasAnyContent($aboutContent, ['about_story_image_url', 'about_story_heading', 'about_story_text']);
?>
<?php if ($showStorySection && ($about === 'all' || $about === 'story')): ?>
    <!-- SECTION 1: OUR STORY -->
    <article id="our-story" class="about-section-item" data-aos="fade-up">
        <?php if (getContentValue($aboutContent, 'about_story_image_url')): ?>
        <div class="about-image-wrapper" data-aos="fade-up-right" data-aos-delay="200">
            <img src="<?php echo getContentValue($aboutContent, 'about_story_image_url', 'text', 'https://media.istockphoto.com/id/153743372/photo/restaurant-table-and-chairs-with-place-settings1.jpg?s=612x612&w=0&k=20&c=jtyCnk3Rx3W1a5RZovPyO1k3BjTvFtCcp1RJzk1_m_k='); ?>" alt="Inspiration behind VINICA's story">
        </div>
        <?php endif; ?>
        <div class="about-text-content" data-aos="fade-left" data-aos-delay="300">
            <?php if (getContentValue($aboutContent, 'about_story_heading')): ?>
            <h2 class="about-section-title"><?php echo getContentValue($aboutContent, 'about_story_heading', 'text', 'Our Story'); ?></h2>
            <div class="title-separator-small"></div>
            <?php endif; ?>
            <?php if (getRawHtmlContent($aboutContent, 'about_story_text')): ?>
            <?php echo getRawHtmlContent($aboutContent, 'about_story_text', 'text', '<p>Default story text...</p>'); ?>
            <?php endif; ?>
        </div>
    </article>
<?php endif; ?>

<?php 
$ambienceItems = [
    ['key_prefix' => 'gh', 'default_name' => 'The Grand Hall', 'default_subtitle' => 'Where Celebrations Come to Life', 'default_text' => '<p>Default Grand Hall text...</p>', 'default_image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80'],
    ['key_prefix' => 'tv', 'default_name' => 'Terrace View', 'default_subtitle' => 'Dining Under the Stars', 'default_text' => '<p>Default Terrace View text...</p>', 'default_image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80'],
    ['key_prefix' => 'll', 'default_name' => 'The Lobby Lounge', 'default_subtitle' => 'First Impressions of Elegance', 'default_text' => '<p>Default Lobby Lounge text...</p>', 'default_image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80'],
    ['key_prefix' => 'pdr', 'default_name' => 'Private Dining Room', 'default_subtitle' => 'Intimacy Meets Indulgence', 'default_text' => '<p>Default Private Dining Room text...</p>', 'default_image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80'],
    ['key_prefix' => 'gc', 'default_name' => 'Garden Courtyard', 'default_subtitle' => 'Tranquility in the Heart of the City', 'default_text' => '<p>Default Garden Courtyard text...</p>', 'default_image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80'],
];

$showAmbienceSection = hasAnyContent($aboutContent, 'about_ambience_main_heading');
foreach ($ambienceItems as $item) {
    if (hasAnyContent($aboutContent, [
        'about_ambience_' . $item['key_prefix'] . '_image_url',
        'about_ambience_' . $item['key_prefix'] . '_name',
        'about_ambience_' . $item['key_prefix'] . '_subtitle',
        'about_ambience_' . $item['key_prefix'] . '_text'
    ])) {
        $showAmbienceSection = true;
        break;
    }
}
?>
<?php if ($showAmbienceSection && ($about === 'all' || $about === 'ambience')): ?>
    <!-- SECTION 2: OUR AMBIENCE -->
    <div id="our-ambience" class="page-wrapper">
        <section class="restaurant-container">
            <?php if (getContentValue($aboutContent, 'about_ambience_main_heading')): ?>
            <header class="main-header" data-aos="fade-up">
                <h1><?php echo getContentValue($aboutContent, 'about_ambience_main_heading', 'text', 'Our Ambience'); ?></h1>
                <div class="diamond-separator">
                    <span class="diamond"></span><span class="diamond"></span><span class="diamond"></span>
                </div>
            </header>
            <?php endif; ?>

            <?php foreach ($ambienceItems as $item): ?>
                <?php 
                $itemKeyPrefix = 'about_ambience_' . $item['key_prefix'];
                $showThisAmbienceItem = hasAnyContent($aboutContent, [
                    $itemKeyPrefix . '_image_url',
                    $itemKeyPrefix . '_name',
                    $itemKeyPrefix . '_subtitle',
                    $itemKeyPrefix . '_text'
                ]);
                ?>
                <?php if ($showThisAmbienceItem): ?>
            <div class="restaurant-item">
                    <?php if (getContentValue($aboutContent, $itemKeyPrefix . '_image_url')) : ?>
                <div class="restaurant-image-wrapper" data-aos="fade-up">
                        <img src="<?php echo getContentValue($aboutContent, $itemKeyPrefix . '_image_url', 'text', $item['default_image']); ?>" alt="<?php echo getContentValue($aboutContent, $itemKeyPrefix . '_name', 'text', $item['default_name']); ?> interior">
                </div>
                    <?php endif; ?>
                    <?php if (getContentValue($aboutContent, $itemKeyPrefix . '_name')) : ?>
                    <h3 class="restaurant-name" data-aos="slide-left"><?php echo getContentValue($aboutContent, $itemKeyPrefix . '_name', 'text', $item['default_name']); ?></h3>
                <div class="restaurant-name-separator" data-aos="slide-left"></div>
                    <?php endif; ?>
                    <?php if (getContentValue($aboutContent, $itemKeyPrefix . '_subtitle')) : ?>
                    <h5 data-aos="slide-right"><i><?php echo getContentValue($aboutContent, $itemKeyPrefix . '_subtitle', 'text', $item['default_subtitle']); ?></i></h5>
                    <?php endif; ?>
                    <?php if (getRawHtmlContent($aboutContent, $itemKeyPrefix . '_text')) : ?>
                    <?php echo getRawHtmlContent($aboutContent, $itemKeyPrefix . '_text', 'text', $item['default_text']); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </section>
    </div>
<?php endif; ?>

<?php 
$teamMembers = [];
for ($i = 1; $i <= 3; $i++) {
    $teamMembers[] = [
        'key_prefix' => 'tm'.$i,
        'default_name' => 'Team Member '.$i,
        'default_role' => 'Role/ID',
        'default_image' => 'https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_640.png'
    ];
}

$showTeamSection = hasAnyContent($aboutContent, ['about_team_main_heading', 'about_team_intro']);
if (!$showTeamSection) {
    foreach ($teamMembers as $member) {
        if (hasAnyContent($aboutContent, [
            'about_team_' . $member['key_prefix'] . '_image_url',
            'about_team_' . $member['key_prefix'] . '_name',
            'about_team_' . $member['key_prefix'] . '_role_or_id'
        ])) {
            $showTeamSection = true;
            break;
        }
    }
}
?>
<?php if ($showTeamSection && ($about === 'all' || $about === 'team')): ?>
    <!-- SECTION 3: OUR TEAM -->
    <div id="our-team" class="our-team-section">
        <section class="restaurant-container">
            <header class="main-header" data-aos="fade-up">
                <?php if (getContentValue($aboutContent, 'about_team_main_heading')): ?>
                <h1><?php echo getContentValue($aboutContent, 'about_team_main_heading', 'text', 'Our Team'); ?></h1>
                <div class="diamond-separator">
                    <span class="diamond"></span><span class="diamond"></span><span class="diamond"></span>
                </div>
                <?php endif; ?>
                <?php if (getRawHtmlContent($aboutContent, 'about_team_intro')): ?>
                <div class="restaurant-item">
                     <?php echo getRawHtmlContent($aboutContent, 'about_team_intro', 'text', '<p>Default team introduction...</p>'); ?>
                </div>
                <?php endif; ?>
            </header>
            
            <?php 
            $displayTeamGrid = false;
            foreach ($teamMembers as $member) {
                if (getContentValue($aboutContent, 'about_team_' . $member['key_prefix'] . '_name')) { // Check if at least one member name is set
                    $displayTeamGrid = true;
                    break;
                }
            }
            ?>
            <?php if ($displayTeamGrid): ?>
            <div class="team-grid">
                <?php foreach ($teamMembers as $member): ?>
                    <?php 
                    $memberKeyPrefix = 'about_team_' . $member['key_prefix'];
                    // Show member card if name is present
                    if (getContentValue($aboutContent, $memberKeyPrefix . '_name')): 
                    ?>
                <div class="team-member-card" data-aos="fade-up">
                        <?php if (getContentValue($aboutContent, $memberKeyPrefix . '_image_url')): ?>
                    <div class="member-avatar-wrapper">
                            <img src="<?php echo getContentValue($aboutContent, $memberKeyPrefix . '_image_url', 'text', $member['default_image']); ?>" alt="<?php echo getContentValue($aboutContent, $memberKeyPrefix . '_name', 'text', $member['default_name']); ?>">
                    </div>
                        <?php endif; ?>
                        <h4 class="member-name"><?php echo getContentValue($aboutContent, $memberKeyPrefix . '_name', 'text', $member['default_name']); ?></h4>
                        <?php if (getContentValue($aboutContent, $memberKeyPrefix . '_role_or_id')): ?>
                        <p class="member-id"><?php echo getContentValue($aboutContent, $memberKeyPrefix . '_role_or_id', 'text', $member['default_role']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/main.php'; 
?>