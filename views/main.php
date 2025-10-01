<?php
require_once 'DAO/pages.php';

$nav_items = get_navbar_items();
$footer_items = get_footer_items();
$footer_buttons = get_footer_buttons();
$logo = get_logo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'VINICA'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($description ?? 'Fine dining restaurant in Ho Chi Minh City offering European cuisine.'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords ?? 'VINICA, fine dining, European cuisine, Ho Chi Minh City'); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://vinica.com<?php echo isset($current_url) ? '/' . $current_url : ''; ?>">
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($logo['slug']); ?>">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/styles.css">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/header.css">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/footer.css">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/login.css">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/home.css">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/services.css">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/menu.css">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/reservation.css">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/about.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container_navbar_fluid">
        <div class="navbar_logo" data-aos="slide-right">
            <a href="/VINICA/home" class="navbar-brand">
                <img src="<?php echo htmlspecialchars($logo['slug']); ?>" alt="Logo" class = "navbar_logo_img">
            </a>
        </div>
        <nav class="navbar navbar-expand-lg sticky-top w-100">
            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#ofcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation" data-aos="slide-left">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="ofcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel" data-aos="slide-right">VINICA</h5>
                    <button type="button" class="navbar-toggler text-reset ms-auto" data-bs-dismiss="offcanvas" aria-label="Close" data-aos="slide-left">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="offcanvas-body" data-aos="slide-right">
                    <ul class="navbar-nav ms-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 500px;" data-aos="fade-up">
                        <?php foreach ($nav_items as $index => $item): ?>
                            <?php if (empty($item['children'])): ?>
                                <?php if (!in_array($item['name'], ['Reservation'])): ?>
                                    <li class="nav-item items" data-aos="fade-up">
                                        <a class="nav-link" href="/VINICA/<?php echo htmlspecialchars($item['slug']); ?>">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php else: ?>
                                <li class="nav-item dropdown" data-aos="fade-up">
                                    <a class="nav-link" href="/VINICA/<?php echo htmlspecialchars($item['slug']); ?>" role="button" aria-expanded="false">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($item['children'] as $child): ?>
                                            <li><a class="dropdown-item" href="/VINICA/<?php echo htmlspecialchars($item['slug']); ?>/<?php echo htmlspecialchars($child['slug']); ?>"><?php echo htmlspecialchars($child['name']); ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex" data-aos="slide-left">
                        <?php foreach ($nav_items as $item): ?>
                            <?php if (in_array($item['name'], ['Reservation'])): ?>
                                <a href="/VINICA/<?php echo htmlspecialchars($item['slug']); ?>" class="btn btn-custom" role="button">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <div>
        <?php echo isset($content) ? $content : ''; ?>                        
    </div>
    <footer class="footer">
        <div class="footer-title" data-aos="flip-up">
            <h3>Food that Warms the Soul</h3>
        </div>
        <div class="text-center mb-4" data-aos="fade-up">
            <?php foreach ($footer_buttons as $button): ?>
                <?php if ($button['order'] == 0): ?>
                    <a href="/VINICA/<?php echo htmlspecialchars($button['slug']); ?>" class="btn btn-custom" role="button">
                        <?php echo htmlspecialchars($button['name']); ?>    
                    </a>
                <?php elseif ($button['order'] == 1): ?>
                    <a href="/VINICA/<?php echo htmlspecialchars($button['slug']); ?>" class="btn btn-custom-outline" role="button">
                        <?php echo htmlspecialchars($button['name']); ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="footer-top ">
            <div class="footer-column logo-column" data-aos="slide-right">
                <img src="<?php echo htmlspecialchars($logo['slug']); ?>" alt="Logo" class = "navbar_logo_img">
            </div>
            <?php foreach ($footer_items as $item): ?>
                <?php if (!empty($item['children'])): ?>
                    <div class="footer-column" data-aos="slide-left">
                        <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                        <ul>
                            <?php foreach ($item['children'] as $child): ?>
                                <li><a href="/VINICA/<?php echo htmlspecialchars($child['slug']); ?>"><?php echo htmlspecialchars($child['name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="footer-column" data-aos="slide-left">
              <h6>FOLLOW US</h6>
              <div class="social-icons">
                <a href="https://www.facebook.com/bichngoc.buithi.3" class="fb-icon" target="_blank" rel="noopener noreferrer"><i class='bx bxl-facebook bx-flip-horizontal bx-tada bx-rotate-90'></i></a>
                <a href="https://www.instagram.com/ngocbui0775?fbclid=IwY2xjawKWuMlleHRuA2FlbQIxMABicmlkETFVeXpLOWE4TkFxbDhBYm1ZAR5p5kdobbOrzkpuG0LIJySCaKg5sL1JfFD4CfzXjSp_QZW9xTlepFG1E8j9yw_aem_CoMWznFIvXtvNJv6vJO3bg" class="ig-icon" target="_blank" rel="noopener noreferrer"><i class='bx bxl-instagram bx-flip-horizontal bx-tada bx-rotate-90'></i></a>
                <a href="https://www.youtube.com/@Ng%E1%BB%8Dc-h2z" class="yt-icon" target="_blank" rel="noopener noreferrer"><i class='bx bxl-youtube bx-flip-horizontal bx-tada bx-rotate-90'></i></a>
              </div>
            </div>
        </div>
        <hr>
        <div class="footer-bottom">
            <p>76 Le Lai Street, District 1, Ho Chi Minh City, Vietnam</p>
            <a href="/VINICA/login" class="login-link">Login</a>
        </div>
    </footer>
    <script src="/VINICA/layout/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script>
        CKEDITOR.replace('main_content_placeholder');
    </script>
</body>
</html>