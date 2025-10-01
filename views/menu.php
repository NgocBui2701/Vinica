<?php
require_once __DIR__ . '/../DAO/MenuDAO.php';
require_once __DIR__ . '/../DAO/pages.php'; // For general page SEO

// $menuDAO = new MenuDAO();
// $pageDAO = new PageDAO(); // Assuming PageDAO is for the 'pages' table SEO

// $menu_categories_to_display = []; // Initialize array for categories to display
// $active_category_slug = null; // To keep track of the requested category slug

// // The router should provide $params['category_slug'] if a specific category is requested.
// // This variable would be extracted if you use extract($params) in your router/index.php
// // For this example, let's assume $category_slug might be set.
// if (isset($category_slug) && is_string($category_slug) && !empty($category_slug)) {
//     $requested_category_slug = $category_slug;
// } elseif (isset($params) && isset($params['category_slug']) && is_string($params['category_slug']) && !empty($params['category_slug'])) {
//     // This handles the case where $params array is passed and contains the slug
//     $requested_category_slug = $params['category_slug'];
// } else {
//     $requested_category_slug = null;
// }

// $page_seo_title = "Our Menu"; // Default title
// $page_seo_meta_description = "Explore VINICA's curated menus, featuring exquisite European cuisine, from delightful lunch sets to elegant dinners and grand buffets."; // Default description
// $page_seo_meta_keywords = "VINICA menu, European cuisine, fine dining, lunch sets, dinner sets, a la carte, buffet, party menu"; // Default keywords

// if ($requested_category_slug) {
//     $singleCategory = $menuDAO->getVisibleCategoryBySlug($requested_category_slug);
//     if ($singleCategory) {
//         $singleCategory['items'] = $menuDAO->getVisibleItemsByCategoryId($singleCategory['id']);
//         $menu_categories_to_display = [$singleCategory];
//         $active_category_slug = $singleCategory['slug'];
        
//         // Update SEO for the specific category
//         $page_seo_title = htmlspecialchars($singleCategory['name']) . " | VINICA Menu";
//         // Use category description for meta description, or a generic one if not available
//         $meta_desc_content = !empty($singleCategory['description']) ? strip_tags($singleCategory['description']) : "Explore the " . htmlspecialchars($singleCategory['name']) . " at VINICA, featuring a selection of our finest European dishes.";
//         if (mb_strlen($meta_desc_content) > 155) {
//             $meta_desc_content = mb_substr($meta_desc_content, 0, 152, 'UTF-8') . '...';
//         }
//         $page_seo_meta_description = htmlspecialchars($meta_desc_content);
//         $page_seo_meta_keywords = htmlspecialchars($singleCategory['name']) . ", VINICA, " . $page_seo_meta_keywords; // Prepend category name to general keywords
//     } else {
//         // Category slug not found or not visible
//         $page_seo_title = "Menu Category Not Found";
//         $page_seo_meta_description = "The menu category you were looking for was not found at VINICA. Please browse our full menu.";
//         $page_seo_meta_keywords = "VINICA, menu category not found, European cuisine";
//         // $menu_categories_to_display remains empty, will show a message or all categories depending on later logic
//         // To show all categories as a fallback:
//         // $allCategories = $menuDAO->getAllVisibleCategories();
//         // foreach ($allCategories as $category) {
//         //     $category['items'] = $menuDAO->getVisibleItemsByCategoryId($category['id']);
//         //     $menu_categories_to_display[] = $category;
//         // }
//     }
// } else {
//     // No specific category slug -> general /menu page, display all categories
//     $allCategories = $menuDAO->getAllVisibleCategories();
//     foreach ($allCategories as $category) {
//         $current_category_items = $menuDAO->getVisibleItemsByCategoryId($category['id']);
//         if (!empty($current_category_items)) { // Only add category if it has items
//             $category['items'] = $current_category_items;
//             $menu_categories_to_display[] = $category;
//         }
//     }
    
//     // Get SEO for the main /menu page from 'pages' table if available
//     $menuPageDetails = $pageDAO->getPageDetailsBySlug('menu');
//     if ($menuPageDetails && !empty($menuPageDetails['title'])) {
//         $page_seo_title = htmlspecialchars($menuPageDetails['title'], ENT_QUOTES, 'UTF-8');
//     }
//     if ($menuPageDetails && !empty($menuPageDetails['meta_description'])) {
//         $page_seo_meta_description = htmlspecialchars($menuPageDetails['meta_description'], ENT_QUOTES, 'UTF-8');
//     }
//     if ($menuPageDetails && !empty($menuPageDetails['meta_keywords'])) {
//         $page_seo_meta_keywords = htmlspecialchars($menuPageDetails['meta_keywords'], ENT_QUOTES, 'UTF-8');
//     }
// }
    $menuPageDetails = get_page_details_by_slug('menu');
    if ($menuPageDetails && !empty($menuPageDetails['title'])) {
        $page_seo_title = htmlspecialchars($menuPageDetails['title'], ENT_QUOTES, 'UTF-8');
    }
    if ($menuPageDetails && !empty($menuPageDetails['meta_description'])) {
        $page_seo_meta_description = htmlspecialchars($menuPageDetails['meta_description'], ENT_QUOTES, 'UTF-8');
    }
    if ($menuPageDetails && !empty($menuPageDetails['meta_keywords'])) {
        $page_seo_meta_keywords = htmlspecialchars($menuPageDetails['meta_keywords'], ENT_QUOTES, 'UTF-8');
    }
// Assign final SEO values for the layout
$title = $page_seo_title . (strpos($page_seo_title, 'VINICA') === false ? " | VINICA" : "");
$metaDescription = $page_seo_meta_description;
$metaKeywords = $page_seo_meta_keywords;

ob_start();
?>
<!-- Hình ảnh background tĩnh ban đầu -->
<div data-aos="fade-up">
        <img src="/VINICA/layout/img/home_1.jpg" alt="Background Image" class="background-image">
    </div> 
    <header class="main-header" data-aos="fade-up">
        <h1>A Symphony of Flavors</h1>
        <div class="diamond-separator">
            <span class="diamond"></span><span class="diamond"></span><span class="diamond"></span>
        </div>
        <p>Explore VINICA's curated menus, crafted with passion and the finest ingredients.</p>
    </header>

    <div class="page-wrapper">
        <section class="main-header menu intro" data-aos="fade-up">
            <h2>Experience European Cuisine Reimagined</h2>
            <div class="diamond-separator-small">
                <span class="diamond"></span><span class="diamond"></span>
            </div>
            <p>Our chefs artfully blend traditional European recipes with modern culinary techniques to create a dining experience that is both comforting and exciting.</p>
        </section>
    </div>
    <!-- Lunch Sets Section -->
    <section id="category-lunch-sets" class="menu-category-section" data-aos="fade-up">
        <header class="category-listing-header menu-header">
            <h2>Our Delightful Lunch Sets</h2>
            <div class="diamond-separator-small center"><span></span><span></span></div>
            <p>Perfectly crafted for a satisfying and elegant midday meal.</p>
        </header>   

        <!-- Set 1 -->
        <section class="intimate-dining-section" data-aos="fade-up">
            <div class="intimate-dining-container">
                <div class="intimate-dining-text me-auto">
                    <h2>Executive Lunch Set</h2>
                    <p><strong>Appetizer (Choose one)</strong></p>
                    <ul>
                        <li>Pan-Seared Scallops with Lemon Butter</li>
                        <li>Classic Caesar Salad with Grilled Chicken</li>  
                    </ul>
                    <p><strong>Main Course (Choose one)</strong></p>
                    <ul>
                        <li>Grilled Salmon, Asparagus, Hollandaise</li>
                        <li>Beef Tenderloin Medallions, Mushroom Ragout</li>
                    </ul>
                    <p><strong>Dessert</strong></p>
                    <ul>
                        <li>Chocolate Lava Cake with Vanilla Ice Cream</li>
                    </ul>
                    <p><em>Includes choice of freshly brewed coffee or artisanal tea.</em></p>
                    <p class="set-menu-price">450,000 VND</p>
                </div>
                <div class="intimate-dining-image img-right">
                    <img decoding="async" width="1920" height="1280" src="https://rrsg.s3.amazonaws.com/wp-content/uploads/2020/03/08154147/Summer-Menu-Dim-Sum.jpg" sizes="(max-width: 1920px) 100vw, 1920px" title="" data-org-title="Executive Lunch Set">
                </div>
            </div>
            <div class="flower img-right">
                <img src="/VINICA/layout/img/menu_bg.png" alt="Flower Decoration">
            </div>
        </section>

        <!-- Set 2 -->
        <section class="intimate-dining-section" data-aos="fade-up" data-aos-delay="100">
            <div class="intimate-dining-container">
                <div class="intimate-dining-text ms-auto">
                    <h2>Light & Fresh Lunch Set</h2>
                    <p><strong>Starter (Choose one)</strong></p>
                    <ul>
                        <li>Creamy Tomato & Basil Soup with Garlic Croutons</li>
                        <li>Mediterranean Quinoa Salad with Feta, Olives & Cucumber</li>
                        <li>Avocado Toast with Poached Egg & Chili Flakes</li>
                    </ul>
                    <p><strong>Main Course (Choose one)</strong></p>
                    <ul>
                        <li>Grilled Chicken Breast with Roasted Vegetables & Lemon-Herb Dressing</li>
                        <li>Pan-Seared Sea Bass with Zucchini Noodles & Cherry Tomato Salsa</li>
                        <li>Lentil & Vegetable Curry with Brown Rice (Vegan)</li>
                    </ul>
                    <p><strong>Dessert</strong></p>
                    <ul>
                        <li>Fresh Seasonal Fruit Platter with Honey-Yogurt Dip</li>
                    </ul>
                    <p><em>Includes a choice of freshly squeezed juice or infused water.</em></p>
                    <p class="set-menu-price">320,000 VND</p>
                </div>
                <div class="intimate-dining-image img-left">
                    <img decoding="async" width="1920" height="1280" src="https://images.lifestyleasia.com/wp-content/uploads/sites/3/2018/07/19160652/334222722_744827937008673_2465361112620825284_n-1350x900.jpg" alt="Executive Lunch Set">
                </div>
            </div>
            <div class="flower img-left">
                <img src="/VINICA/layout/img/menu_bg.png" alt="Flower Decoration">
            </div>
        </section>
    </section>

    <!-- Dinner Sets Section -->
    <section id="category-dinner-sets" class="menu-category-section" data-aos="fade-up">
        <header class="category-listing-header menu-header">
            <h2>Elegant Dinner Sets</h2>
            <div class="diamond-separator-small center"><span></span><span></span></div>
            <p>Refined multi-course dinners perfect for an indulgent evening meal.</p>
        </header>

        <!-- Set 1 -->
        <section class="intimate-dining-section" data-aos="fade-up">
            <div class="intimate-dining-container">
                <div class="intimate-dining-text me-auto">
                    <h2>Gourmet Steak Dinner</h2>
                    <p><strong>Starter</strong></p>
                    <ul>
                        <li>Truffle Mushroom Soup with Parmesan Crisp</li>
                        <li>Smoked Duck Breast Salad with Raspberry Vinaigrette</li>
                    </ul>
                    <p><strong>Main Course</strong></p>
                    <ul>
                        <li>Chargrilled Ribeye with Béarnaise Sauce, Gratin Dauphinois</li>
                        <li>Filet Mignon with Red Wine Reduction, Roasted Root Vegetables</li>
                    </ul>
                    <p><strong>Dessert</strong></p>
                    <ul>
                        <li>Classic Crème Brûlée with Vanilla Bean</li>
                    </ul>
                    <p><em>Includes your choice of house wine or sparkling water.</em></p>
                    <p class="set-menu-price">750,000 VND</p>
                </div>
                <div class="intimate-dining-image img-right">
                    <img src="https://media.dolenglish.vn/PUBLIC/MEDIA/fa472034-f0b5-43e8-aac5-360f4dcce0c2.jpg" alt="Gourmet Steak Dinner">
                </div>
            </div>
            <div class="flower img-right">
                <img src="/VINICA/layout/img/menu_bg.png" alt="Flower Decoration">
            </div>
        </section>

        <!-- Set 2 -->
        <section class="intimate-dining-section" data-aos="fade-up" data-aos-delay="100">
            <div class="intimate-dining-container">
                <div class="intimate-dining-text ms-auto">
                    <h2>Seafood Indulgence Set</h2>
                    <p><strong>Starter</strong></p>
                    <ul>
                        <li>Lobster Bisque with Cognac Cream</li>
                        <li>Grilled Octopus with Lemon & Capers</li>
                    </ul>
                    <p><strong>Main Course</strong></p>
                    <ul>
                        <li>Butter-Poached Lobster Tail, Garlic Mashed Potatoes</li>
                        <li>Seared Sea Bass with Saffron Risotto & Baby Spinach</li>
                    </ul>
                    <p><strong>Dessert</strong></p>
                    <ul>
                        <li>Lemon Tart with Raspberry Coulis</li>
                    </ul>
                    <p><em>Includes a glass of house white wine or chilled tea.</em></p>
                    <p class="set-menu-price">820,000 VND</p>
                </div>
                <div class="intimate-dining-image img-left">
                    <img src="https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480/img/recipe/ras/Assets/4ad8a1d1142b720368434d8267d71407/Derivates/939409616649ee117d1a0d696df985ac2a4a156d.jpg" alt="Seafood Indulgence Set">
                </div>
            </div>
            <div class="flower img-left">
                <img src="/VINICA/layout/img/menu_bg.png" alt="Flower Decoration">
            </div>
        </section>

        <!-- Set 3 -->
        <section class="intimate-dining-section" data-aos="fade-up" data-aos-delay="200">
            <div class="intimate-dining-container">
                <div class="intimate-dining-text me-auto">
                    <h2>Vegetarian Delight Dinner</h2>
                    <p><strong>Starter</strong></p>
                    <ul>
                        <li>Heirloom Tomato Carpaccio with Balsamic Glaze</li>
                        <li>Grilled Zucchini Roll-ups with Ricotta</li>
                    </ul>
                    <p><strong>Main Course</strong></p>
                    <ul>
                        <li>Wild Mushroom Risotto with Truffle Oil</li>
                        <li>Eggplant Parmesan with Basil Pesto</li>
                    </ul>
                    <p><strong>Dessert</strong></p>
                    <ul>
                        <li>Vanilla Panna Cotta with Berry Compote</li>
                    </ul>
                    <p><em>Includes choice of herbal tea or sparkling lemonade.</em></p>
                    <p class="set-menu-price">590,000 VND</p>
                </div>
                <div class="intimate-dining-image img-right">
                    <img src="https://i.cbc.ca/1.5018510.1553797309!/fileImage/httpImage/image.jpg_gen/derivatives/16x9_1180/vegetarian-meal.jpg?im=Resize%3D620" alt="Vegetarian Dinner Set">
                </div>
            </div>
            <div class="flower img-right">
                <img src="/VINICA/layout/img/menu_bg.png" alt="Flower Decoration">
            </div>
        </section>
    </section>

    <!-- À La Carte Menu Section -->
    <section id="category-a-la-carte" class="menu-category-section mx-5" data-aos="fade-up">
        <header class="category-listing-header menu-header">
            <h2>À La Carte Menu</h2>
            <div class="diamond-separator-small center"><span></span><span></span></div>
            <p>Pick and choose your favorites from our chef-curated selection of European delights.</p>
        </header>

        <div class="row g-4">
            <!-- Dish 1 -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="ratio ratio-4x3">
                        <img src="https://athomewithrebecka.com/wp-content/uploads/2024/06/1200x1200Rebecka-Plate.jpg" class="card-img-top object-fit-cover" alt="Grilled Iberico Pork Chop">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Grilled Iberico Pork Chop</h5>
                        <p class="card-text">Apple compote, Dijon mustard jus, herbed potatoes.</p>
                        <p class="fw-bold text-warning">490,000 VND</p>
                    </div>
                </div>
            </div>
        
            <!-- Dish 2 -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="ratio ratio-4x3">
                        <img src="https://www.luvaduck.com.au/wp-content/uploads/2024/10/LUV16094_Recipe_Thumbnails_Confit_600x600px.jpg" class="card-img-top object-fit-cover" alt="Duck Confit with Lentils">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Duck Confit with Lentils</h5>
                        <p class="card-text">Slow-cooked duck leg, rosemary jus, green lentils.</p>
                        <p class="fw-bold text-warning">450,000 VND</p>
                    </div>
                </div>
            </div>
        
            <!-- Dish 3 -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="ratio ratio-4x3">
                        <img src="https://www.nzherald.co.nz/resizer/Hef090OccJbBD7j3u6bvQNFXl-Y=/arc-anglerfish-syd-prod-nzme/public/OMKV7PYXS6LKLRWWTTZBIIMC4Y.jpg" class="card-img-top object-fit-cover" alt="Seafood Paella">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Seafood Paella</h5>
                        <p class="card-text">Saffron rice with shrimp, mussels, calamari, and clams.</p>
                        <p class="fw-bold text-warning">520,000 VND</p>
                    </div>
                </div>
            </div>
        
            <!-- Dish 4 -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="ratio ratio-4x3">
                        <img src="https://center-of-the-plate.com/wp-content/uploads/2016/11/seared-foie-gras.jpg?w=1000" class="card-img-top object-fit-cover" alt="Pan-Seared Foie Gras">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Pan-Seared Foie Gras</h5>
                        <p class="card-text">Served on brioche toast with fig compote & balsamic glaze.</p>
                        <p class="fw-bold text-warning">580,000 VND</p>
                    </div>
                </div>
                </div>
            </div>
    </section>

    <!-- Party Menu Section -->
    <section id="category-party" class="menu-category-section" data-aos="fade-up">
        <header class="category-listing-header menu-header">
            <h2>Party Menu</h2>
            <div class="diamond-separator-small center"><span></span><span></span></div>
            <p>Celebrate with our elegant European party sets designed to impress.</p>
        </header>
        
        <!-- Party Set 1 -->
        <section class="intimate-dining-section" data-aos="fade-up" data-aos-delay="100">
            <div class="intimate-dining-container">
                <div class="intimate-dining-text me-auto">
                    <h2>Gourmet Canapés Selection</h2>
                    <p>An elegant array of bite-sized masterpieces, perfect for cocktail receptions, networking events, or as a prelude to a larger celebration. Choose your desired quantity per guest.</p>
                    <p><strong>Sample Savory Canapés (Choose from a wider list)</strong></p>
                    <ul>
                        <li>Smoked Salmon Rosettes on Dill Blinis</li>
                        <li>Miniature Beef Wellingtons with Horseradish Cream</li>
                        <li>Wild Mushroom & Truffle Vol-au-vents</li>
                        <li>Caprese Skewers with Balsamic Glaze</li>
                        <li>Spicy Tuna Tartare on Crispy Wonton</li>
                    </ul>
                    <p><strong>Sample Sweet Canapés</strong></p>
                    <ul>
                        <li>Miniature Lemon Meringue Tarts</li>
                        <li>Dark Chocolate & Raspberry Bites</li>
                        <li>Assorted Macarons</li>
                    </ul>
                    <p class="set-menu-includes"><em>Minimum order of 20 guests. Customizable options available.</em></p>
                    <p class="set-menu-price">Starting from 600,000 VND++ per person (for 5 canapés)</p>
                </div>
                <div class="intimate-dining-image img-right">
                    <img decoding="async" width="1920" height="1280" src="https://cdn0.hitched.co.uk/article/7288/original/1280/jpg/158827-11-blame-frank-canape-selection.jpeg" alt="Gourmet Canapés Selection">
                </div>
            </div>
            <div class="flower img-right">
                <img src="/VINICA/layout/img/menu_bg.png" alt="Flower Decoration">
            </div>
        </section>

        <!-- Party Set 2 -->
        <section class="intimate-dining-section" data-aos="fade-up" data-aos-delay="200">
            <div class="intimate-dining-container">
                <div class="intimate-dining-text ms-auto">
                    <h2>Grand Celebration Set Menu</h2>
                    <p>A lavish multi-course menu designed for larger group celebrations, offering a variety of choices to please every palate. Ideal for significant birthdays, anniversaries, or corporate milestones.</p>
                    <p><strong>Appetizer (Family Style Sharing or Plated)</strong></p>
                    <ul><li>Selection of Artisan Cured Meats & Cheeses, Marinated Olives, Grilled Vegetables</li></ul>
                    <p><strong>Soup (Choose one)</strong></p>
                    <ul><li>Cream of Asparagus with Crème Fraîche</li><li>Clear Chicken Consommé with Herb Dumplings</li></ul>
                    <p><strong>Main Course (Pre-select one per guest or offer choices)</strong></p>
                    <ul>
                        <li>Roasted Rack of Lamb with Rosemary Jus & Dauphinoise Potatoes</li>
                        <li>Pan-Fried Barramundi with Lemon-Caper Sauce & Sautéed Greens</li>
                        <li>Porcini Mushroom Risotto with Parmesan Crisp (Vegetarian)</li>
                    </ul>
                    <p><strong>Dessert Buffet or Plated Trio</strong></p>
                    <ul><li>Assortment of VINICA's signature cakes, pastries, and fresh fruits.</li></ul>
                    <p class="set-menu-includes"><em>Beverage packages available. Minimum 30 guests.</em></p>
                    <p class="set-menu-price">Starting from 1,200,000 VND++ per person</p>
                </div>
                <div class="intimate-dining-image img-left">
                    <img decoding="async" width="1920" height="1280" src="https://eddyskitchen.wordpress.com/wp-content/uploads/2011/08/roasted-leg-of-lamb-with-mushroom-saffron-risotto1.jpg" alt="Grand Celebration Set Menu">
                </div>
            </div>
            <div class="flower img-left">
                <img src="/VINICA/layout/img/menu_bg.png" alt="Flower Decoration Alt">
            </div>
        </section>
        
        <!-- Buffet Menu Section -->
        <section id="category-buffet" class="menu-category-section" data-aos="fade-up">
            <header class="category-listing-header menu-header">
                <h2>Buffet Menu</h2>
                <div class="diamond-separator-small center"><span></span><span></span></div>
                <p>An opulent European buffet experience, ideal for grand gatherings and celebrations.</p>
            </header>
            
            <!-- Buffet Set 1 -->
            <section class="intimate-dining-section" data-aos="fade-up">
                <div class="intimate-dining-container">
                    <div class="intimate-dining-text" style="width: 100%;">
                        <h2 class="text-center">Imperial Feast Buffet</h2>
                        <div class="row">
                            <div class="col-md-5 mx-auto">
                                <p><strong>Appetizers & Cold Selections</strong></p>
                                <ul>
                                    <li>Smoked Salmon with Dill & Capers</li>
                                    <li>Duck Liver Pâté on Crostini</li>
                                    <li>Assorted European Cheese Platter</li>
                                    <li>Prosciutto & Melon</li>
                                    <li>Caprese Salad</li>
                                    <li>Marinated Olives & Artichokes</li>
                                    <li>Truffle Deviled Eggs</li>
                                    <li>Fruit Gazpacho</li>
                                    <li>Mini Quiche Lorraine</li>
                                    <li>Cold Pasta Salad</li>
                                </ul>
                                <p><strong>Soups & Salads</strong></p>
                                <ul>
                                    <li>French Onion Soup</li>
                                    <li>Wild Mushroom Velouté</li>
                                    <li>Caesar Salad with Parmesan</li>
                                    <li>German Potato Salad</li>
                                </ul>
                                <p><strong>Live Station</strong></p>
                                <ul>
                                    <li>Carved Roasted Prime Rib</li>
                                    <li>Made-to-order Pasta (Carbonara, Pesto, Pomodoro)</li>
                                </ul>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center mx-auto">
                                <div style="border-left: 1px solid #a88c51; height: 80%;"></div>
                            </div>
                            <div class="col-md-5 mx-auto">
                                <p><strong>Hot Dishes</strong></p>
                                <ul>
                                    <li>Herb-Roasted Chicken</li>
                                    <li>Beef Bourguignon</li>
                                    <li>Seafood Paella</li>
                                    <li>Grilled Salmon with Hollandaise</li>
                                    <li>Buttered Seasonal Vegetables</li>
                                    <li>Garlic Mashed Potatoes</li>
                                    <li>Vegetable Lasagna</li>
                                    <li>Spinach Gratin</li>
                                    <li>Roasted Root Vegetables</li>
                                    <li>Pasta with Pesto Cream Sauce</li>
                                </ul>
                                <p><strong>Desserts</strong></p>
                                <ul>
                                    <li>Mini Tiramisu</li>
                                    <li>Chocolate Fountain with Fruits & Marshmallows</li>
                                    <li>Panna Cotta with Berry Coulis</li>
                                    <li>Lemon Tartlets</li>
                                    <li>Crème Brûlée</li>
                                    <li>Assorted Macarons</li>
                                    <li>Profiteroles</li>
                                </ul>
                            </div>
                        </div>
                        <p class="set-menu-price">1,200,000 VND / person</p>
                    </div>
                </div>
                <div class="flower img-right">
                    <img src="/VINICA/layout/img/menu_bg.png" alt="Flower Decoration">
                </div>
            </section>
            
            <!-- Buffet Set 2 -->
            <section class="intimate-dining-section section-right" data-aos="fade-up" data-aos-delay="100">
                <div class="intimate-dining-container">
                    <div class="intimate-dining-text" style="width: 100%;">
                        <h2 class="text-center">Château Royale Buffet</h2>
                        <div class="row">
                            <div class="col-md-5 mx-auto">
                                <p><strong>Appetizers & Cold Cuts</strong></p>
                                <ul>
                                    <li>Marinated Artichokes & Olives</li>
                                    <li>Gravlax with Mustard-Dill Sauce</li>
                                    <li>Beef Carpaccio with Arugula</li>
                                    <li>Truffle Deviled Eggs</li>
                                    <li>Cheese & Charcuterie Tower</li>
                                </ul>
                                <p><strong>Hot Selections</strong></p>
                                <ul>
                                    <li>Coq au Vin</li>
                                    <li>Lamb Chops Provençal</li>
                                    <li>Grilled Sea Bass with Lemon Butter</li>
                                    <li>Risotto with Porcini Mushrooms</li>
                                    <li>Gnocchi in Gorgonzola Sauce</li>
                                    <li>Ratatouille</li>
                                    <li>Steamed Asparagus with Hollandaise</li>
                                </ul>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center mx-auto">
                                <div style="border-left: 1px solid #a88c51; height: 80%;"></div>
                            </div>
                            <div class="col-md-5 mx-auto">
                                <p><strong>Live Grilling Station</strong></p>
                                <ul>
                                    <li>Australian Ribeye</li>
                                    <li>Garlic Butter Prawns</li>
                                </ul>
                                <p><strong>Desserts</strong></p>
                                <ul>
                                    <li>Crème Brûlée</li>
                                    <li>Chocolate Mousse Domes</li>
                                    <li>Profiteroles</li>
                                    <li>Fruit Tartlets</li>
                                    <li>French Madeleine Cookies</li>
                                </ul>
                            </div>
                        </div>
                        <p class="set-menu-price">1,450,000 VND / person</p>
                    </div>
                </div>
                <div class="flower img-right">
                    <img src="/VINICA/layout/img/menu_bg.png" alt="Flower Decoration">
                </div>
            </section>
        </section>
    </section>

<?php
$content = ob_get_clean();
require __DIR__ . '/main.php'; 
?>