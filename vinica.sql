-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 26, 2025 lúc 05:53 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `vinica`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `attempt_time`) VALUES
(30, 'ngocbui', '2025-05-26 07:17:58'),
(31, 'ngocbui', '2025-05-26 07:33:15'),
(32, 'ngocbui', '2025-05-26 07:34:56'),
(33, 'ngocbui', '2025-05-26 07:39:37'),
(34, 'ngocbui', '2025-05-26 08:16:16'),
(35, 'ngocbui', '2025-05-26 08:51:50'),
(36, 'ngocbui', '2025-05-26 09:48:15'),
(37, 'ngocbui', '2025-05-26 10:20:40'),
(38, 'ngocbui', '2025-05-26 11:22:11'),
(39, 'ngocbui', '2025-05-26 12:54:19'),
(40, 'ngocbui', '2025-05-26 13:25:46'),
(41, 'ngocbui', '2025-05-26 14:55:30'),
(42, 'ngocbui', '2025-05-26 15:41:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_visible` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `name`, `slug`, `description`, `display_order`, `is_visible`, `created_at`, `updated_at`) VALUES
(1, 'Lunch Sets', 'lunch-sets', 'Perfectly crafted for a satisfying and elegant midday meal.', 1, 1, '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(2, 'Dinner Sets', 'dinner-sets', 'Refined multi-course dinners perfect for an indulgent evening meal.', 2, 1, '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(3, 'À La Carte Menu', 'a-la-carte', 'Pick and choose your favorites from our chef-curated selection of European delights.', 3, 1, '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(4, 'Party Menu', 'party-menu', 'Celebrate with our elegant European party sets designed to impress.', 4, 1, '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(5, 'Buffet Menu', 'buffet-menu', 'An opulent European buffet experience, ideal for grand gatherings and celebrations.', 5, 1, '2025-05-26 12:26:37', '2025-05-26 12:26:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description_short` varchar(500) DEFAULT NULL,
  `description_long` text DEFAULT NULL,
  `price_amount` decimal(12,2) DEFAULT NULL,
  `price_currency` varchar(10) DEFAULT 'VND',
  `price_text_prefix` varchar(50) DEFAULT NULL,
  `price_text_suffix` varchar(100) DEFAULT NULL,
  `image_url` varchar(512) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_visible` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `tags` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `slug`, `description_short`, `description_long`, `price_amount`, `price_currency`, `price_text_prefix`, `price_text_suffix`, `image_url`, `display_order`, `is_visible`, `is_featured`, `tags`, `notes`, `created_at`, `updated_at`) VALUES
(3, 1, 'Executive Lunch Set', 'executive-lunch-set', NULL, '<p><strong>Appetizer (Choose one)</strong></p><ul><li>Pan-Seared Scallops with Lemon Butter</li><li>Classic Caesar Salad with Grilled Chicken</li></ul><p><strong>Main Course (Choose one)</strong></p><ul><li>Grilled Salmon, Asparagus, Hollandaise</li><li>Beef Tenderloin Medallions, Mushroom Ragout</li></ul><p><strong>Dessert</strong></p><ul><li>Chocolate Lava Cake with Vanilla Ice Cream</li></ul>', 450000.00, 'VND', NULL, NULL, 'https://rrsg.s3.amazonaws.com/wp-content/uploads/2020/03/08154147/Summer-Menu-Dim-Sum.jpg', 1, 1, 0, NULL, '<em>Includes choice of freshly brewed coffee or artisanal tea.</em>', '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(4, 1, 'Light & Fresh Lunch Set', 'light-fresh-lunch-set', NULL, '<p><strong>Starter (Choose one)</strong></p><ul><li>Creamy Tomato & Basil Soup with Garlic Croutons</li><li>Mediterranean Quinoa Salad with Feta, Olives & Cucumber</li><li>Avocado Toast with Poached Egg & Chili Flakes</li></ul><p><strong>Main Course (Choose one)</strong></p><ul><li>Grilled Chicken Breast with Roasted Vegetables & Lemon-Herb Dressing</li><li>Pan-Seared Sea Bass with Zucchini Noodles & Cherry Tomato Salsa</li><li>Lentil & Vegetable Curry with Brown Rice (Vegan)</li></ul><p><strong>Dessert</strong></p><ul><li>Fresh Seasonal Fruit Platter with Honey-Yogurt Dip</li></ul>', 320000.00, 'VND', NULL, NULL, 'https://images.lifestyleasia.com/wp-content/uploads/sites/3/2018/07/19160652/334222722_744827937008673_2465361112620825284_n-1350x900.jpg', 2, 1, 0, NULL, '<em>Includes a choice of freshly squeezed juice or infused water.</em>', '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(5, 2, 'Gourmet Steak Dinner', 'gourmet-steak-dinner', NULL, '<p><strong>Starter</strong></p><ul><li>Truffle Mushroom Soup with Parmesan Crisp</li><li>Smoked Duck Breast Salad with Raspberry Vinaigrette</li></ul><p><strong>Main Course</strong></p><ul><li>Chargrilled Ribeye with Béarnaise Sauce, Gratin Dauphinoise</li><li>Filet Mignon with Red Wine Reduction, Roasted Root Vegetables</li></ul><p><strong>Dessert</strong></p><ul><li>Classic Crème Brûlée with Vanilla Bean</li></ul>', 750000.00, 'VND', NULL, NULL, 'https://media.dolenglish.vn/PUBLIC/MEDIA/fa472034-f0b5-43e8-aac5-360f4dcce0c2.jpg', 1, 1, 0, NULL, '<em>Includes your choice of house wine or sparkling water.</em>', '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(6, 2, 'Seafood Indulgence Set', 'seafood-indulgence-set', NULL, '<p><strong>Starter</strong></p><ul><li>Lobster Bisque with Cognac Cream</li><li>Grilled Octopus with Lemon & Capers</li></ul><p><strong>Main Course</strong></p><ul><li>Butter-Poached Lobster Tail, Garlic Mashed Potatoes</li><li>Seared Sea Bass with Saffron Risotto & Baby Spinach</li></ul><p><strong>Dessert</strong></p><ul><li>Lemon Tart with Raspberry Coulis</li></ul>', 820000.00, 'VND', NULL, NULL, 'https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480/img/recipe/ras/Assets/4ad8a1d1142b720368434d8267d71407/Derivates/939409616649ee117d1a0d696df985ac2a4a156d.jpg', 2, 1, 0, NULL, '<em>Includes a glass of house white wine or chilled tea.</em>', '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(7, 2, 'Vegetarian Delight Dinner', 'vegetarian-delight-dinner', NULL, '<p><strong>Starter</strong></p><ul><li>Heirloom Tomato Carpaccio with Balsamic Glaze</li><li>Grilled Zucchini Roll-ups with Ricotta</li></ul><p><strong>Main Course</strong></p><ul><li>Wild Mushroom Risotto with Truffle Oil</li><li>Eggplant Parmesan with Basil Pesto</li></ul><p><strong>Dessert</strong></p><ul><li>Vanilla Panna Cotta with Berry Compote</li></ul>', 590000.00, 'VND', NULL, NULL, 'https://i.cbc.ca/1.5018510.1553797309!/fileImage/httpImage/image.jpg_gen/derivatives/16x9_1180/vegetarian-meal.jpg?im=Resize%3D620', 3, 1, 0, NULL, '<em>Includes choice of herbal tea or sparkling lemonade.</em>', '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(8, 3, 'Grilled Iberico Pork Chop', 'grilled-iberico-pork-chop', 'Apple compote, Dijon mustard jus, herbed potatoes.', NULL, 490000.00, 'VND', NULL, NULL, 'https://athomewithrebecka.com/wp-content/uploads/2024/06/1200x1200Rebecka-Plate.jpg', 1, 1, 0, NULL, NULL, '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(9, 3, 'Duck Confit with Lentils', 'duck-confit-with-lentils', 'Slow-cooked duck leg, rosemary jus, green lentils.', NULL, 450000.00, 'VND', NULL, NULL, 'https://www.luvaduck.com.au/wp-content/uploads/2024/10/LUV16094_Recipe_Thumbnails_Confit_600x600px.jpg', 2, 1, 0, NULL, NULL, '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(10, 3, 'Seafood Paella', 'seafood-paella', 'Saffron rice with shrimp, mussels, calamari, and clams.', NULL, 520000.00, 'VND', NULL, NULL, 'https://www.nzherald.co.nz/resizer/Hef090OccJbBD7j3u6bvQNFXl-Y=/arc-anglerfish-syd-prod-nzme/public/OMKV7PYXS6LKLRWWTTZBIIMC4Y.jpg', 3, 1, 0, NULL, NULL, '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(11, 3, 'Pan-Seared Foie Gras', 'pan-seared-foie-gras', 'Served on brioche toast with fig compote & balsamic glaze.', NULL, 580000.00, 'VND', NULL, NULL, 'https://center-of-the-plate.com/wp-content/uploads/2016/11/seared-foie-gras.jpg?w=1000', 4, 1, 0, NULL, NULL, '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(12, 4, 'Gourmet Canapés Selection', 'gourmet-canapes-selection', NULL, '<p>An elegant array of bite-sized masterpieces, perfect for cocktail receptions, networking events, or as a prelude to a larger celebration. Choose your desired quantity per guest.</p><p><strong>Sample Savory Canapés (Choose from a wider list)</strong></p><ul><li>Smoked Salmon Rosettes on Dill Blinis</li><li>Miniature Beef Wellingtons with Horseradish Cream</li><li>Wild Mushroom & Truffle Vol-au-vents</li><li>Caprese Skewers with Balsamic Glaze</li><li>Spicy Tuna Tartare on Crispy Wonton</li></ul><p><strong>Sample Sweet Canapés</strong></p><ul><li>Miniature Lemon Meringue Tarts</li><li>Dark Chocolate & Raspberry Bites</li><li>Assorted Macarons</li></ul>', 600000.00, 'VND', 'Starting from', '++ per person (for 5 canapés)', 'https://cdn0.hitched.co.uk/article/7288/original/1280/jpg/158827-11-blame-frank-canape-selection.jpeg', 1, 1, 0, NULL, '<em>Minimum order of 20 guests. Customizable options available.</em>', '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(13, 4, 'Grand Celebration Set Menu', 'grand-celebration-set-menu', NULL, '<p>A lavish multi-course menu designed for larger group celebrations, offering a variety of choices to please every palate. Ideal for significant birthdays, anniversaries, or corporate milestones.</p><p><strong>Appetizer (Family Style Sharing or Plated)</strong></p><ul><li>Selection of Artisan Cured Meats & Cheeses, Marinated Olives, Grilled Vegetables</li></ul><p><strong>Soup (Choose one)</strong></p><ul><li>Cream of Asparagus with Crème Fraîche</li><li>Clear Chicken Consommé with Herb Dumplings</li></ul><p><strong>Main Course (Pre-select one per guest or offer choices)</strong></p><ul><li>Roasted Rack of Lamb with Rosemary Jus & Dauphinoise Potatoes</li><li>Pan-Fried Barramundi with Lemon-Caper Sauce & Sautéed Greens</li><li>Porcini Mushroom Risotto with Parmesan Crisp (Vegetarian)</li></ul><p><strong>Dessert Buffet or Plated Trio</strong></p><ul><li>Assortment of VINICA\'s signature cakes, pastries, and fresh fruits.</li></ul>', 1200000.00, 'VND', 'Starting from', '++ per person', 'https://eddyskitchen.wordpress.com/wp-content/uploads/2011/08/roasted-leg-of-lamb-with-mushroom-saffron-risotto1.jpg', 2, 1, 0, NULL, '<em>Beverage packages available. Minimum 30 guests.</em>', '2025-05-26 12:26:37', '2025-05-26 12:26:37'),
(14, 5, 'Imperial Feast Buffet', 'imperial-feast-buffet', NULL, '<div class=\"row\"><div class=\"col-md-5 mx-auto\"><p><strong>Appetizers & Cold Selections</strong></p><ul><li>Smoked Salmon with Dill & Capers</li><li>Duck Liver Pâté on Crostini</li><li>Assorted European Cheese Platter</li><li>Prosciutto & Melon</li><li>Caprese Salad</li><li>Marinated Olives & Artichokes</li><li>Truffle Deviled Eggs</li><li>Fruit Gazpacho</li><li>Mini Quiche Lorraine</li><li>Cold Pasta Salad</li></ul><p><strong>Soups & Salads</strong></p><ul><li>French Onion Soup</li><li>Wild Mushroom Velouté</li><li>Caesar Salad with Parmesan</li><li>German Potato Salad</li></ul><p><strong>Live Station</strong></p><ul><li>Carved Roasted Prime Rib</li><li>Made-to-order Pasta (Carbonara, Pesto, Pomodoro)</li></ul></div><div class=\"col-md-1 d-flex justify-content-center mx-auto\"><div style=\"border-left: 1px solid #a88c51; height: 80%;\"></div></div><div class=\"col-md-5 mx-auto\"><p><strong>Hot Dishes</strong></p><ul><li>Herb-Roasted Chicken</li><li>Beef Bourguignon</li><li>Seafood Paella</li><li>Grilled Salmon with Hollandaise</li><li>Buttered Seasonal Vegetables</li><li>Garlic Mashed Potatoes</li><li>Vegetable Lasagna</li><li>Spinach Gratin</li><li>Roasted Root Vegetables</li><li>Pasta with Pesto Cream Sauce</li></ul><p><strong>Desserts</strong></p><ul><li>Mini Tiramisu</li><li>Chocolate Fountain with Fruits & Marshmallows</li><li>Panna Cotta with Berry Coulis</li><li>Lemon Tartlets</li><li>Crème Brûlée</li><li>Assorted Macarons</li><li>Profiteroles</li></ul></div></div>', 1200000.00, 'VND', NULL, '/ person', NULL, 1, 1, 0, NULL, NULL, '2025-05-26 12:26:38', '2025-05-26 12:26:38'),
(15, 5, 'Château Royale Buffet', 'chateau-royale-buffet', NULL, '<div class=\"row\"><div class=\"col-md-5 mx-auto\"><p><strong>Appetizers & Cold Cuts</strong></p><ul><li>Marinated Artichokes & Olives</li><li>Gravlax with Mustard-Dill Sauce</li><li>Beef Carpaccio with Arugula</li><li>Truffle Deviled Eggs</li><li>Cheese & Charcuterie Tower</li></ul><p><strong>Hot Selections</strong></p><ul><li>Coq au Vin</li><li>Lamb Chops Provençal</li><li>Grilled Sea Bass with Lemon Butter</li><li>Risotto with Porcini Mushrooms</li><li>Gnocchi in Gorgonzola Sauce</li><li>Ratatouille</li><li>Steamed Asparagus with Hollandaise</li></ul></div><div class=\"col-md-1 d-flex justify-content-center mx-auto\"><div style=\"border-left: 1px solid #a88c51; height: 80%;\"></div></div><div class=\"col-md-5 mx-auto\"><p><strong>Live Grilling Station</strong></p><ul><li>Australian Ribeye</li><li>Garlic Butter Prawns</li></ul><p><strong>Desserts</strong></p><ul><li>Crème Brûlée</li><li>Chocolate Mousse Domes</li><li>Profiteroles</li><li>Fruit Tartlets</li><li>French Madeleine Cookies</li></ul></div></div>', 1450000.00, 'VND', NULL, '/ person', NULL, 2, 1, 0, NULL, NULL, '2025-05-26 12:26:38', '2025-05-26 12:26:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 'home', 'Welcome to VINICA | Premier Fine Dining', 'Discover VINICA, a premier fine dining restaurant in Ho Chi Minh City.', 'fine dining, European cuisine, Ho Chi Minh City', '2025-05-24 09:10:32', '2025-05-26 15:45:46'),
(2, 'about', 'About VINICA | Fine Dining European Restaurant in Ho Chi Minh City', 'Learn about VINICA\'s story, elegant ambience, and dedicated team. Experience European culinary artistry and heartfelt hospitality in Ho Chi Minh City.', 'VINICA, fine dining Ho Chi Minh, European restaurant, our story, our ambience, our team, luxury dining, European cuisine', '2025-05-24 09:10:32', '2025-05-26 08:39:26'),
(3, 'menu', 'Our Menu', 'Explore VINICA’s curated menus crafted with passion.', 'menu, European cuisine, fine dining', '2025-05-24 09:10:32', '2025-05-24 09:10:32'),
(4, 'services', 'Event Venues and Services in Ho Chi Minh City | VINICA', 'Explore elegant and versatile event venues at VINICA, Ho Chi Minh City. Perfect for birthday parties, corporate events, weddings, year-end parties, and private dining. Book your exceptional experience today.', 'VINICA, event venues, party venues HCMC, corporate events, wedding reception, birthday party, private dining, Ho Chi Minh City, event spaces, function rooms', '2025-05-24 09:10:32', '2025-05-26 11:23:23'),
(5, 'reservation', 'Reservation', 'Make a reservation at VINICA for a fine dining experience.', 'reservation, fine dining, VINICA', '2025-05-24 09:10:32', '2025-05-24 09:10:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `page_content`
--

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL,
  `page_slug` varchar(100) NOT NULL,
  `section_key` varchar(100) NOT NULL,
  `content_type` enum('text','textarea','image_url','video_url','link_href','link_text','html') NOT NULL DEFAULT 'text',
  `content_value_text` text DEFAULT NULL,
  `item_order` int(11) DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `page_content`
--

INSERT INTO `page_content` (`id`, `page_slug`, `section_key`, `content_type`, `content_value_text`, `item_order`, `is_visible`, `created_at`, `updated_at`) VALUES
(1, 'home', 'hero_image_url', 'image_url', '/VINICA/layout/img/uploads/home/hero_image_url_68341acd9c19e5.81099275.jpg', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(2, 'home', 'intro_main_heading', 'text', 'A Taste of Europe, A Touch of Elegance', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(3, 'home', 'art_dining_heading', 'text', 'The Art of Fine Dining', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(4, 'home', 'art_dining_text', 'html', '<p>Experience the charm of Europe with a refined<em> </em>ambiance, tailored for intimate dinners, joyful gatherings, and exclusive <strong>occasions.&nbsp;</strong></p>', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(5, 'home', 'art_dining_image_url', 'image_url', 'https://shbmastercardworld.com.vn/wp-content/uploads/2024/02/img-resize-1-1.png', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(6, 'home', 'events_heading', 'text', 'Private Events & Celebrations', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(7, 'home', 'events_text', 'html', '<p>Whether it\'s a birthday party, corporate gathering, or year-end celebration, we provide bespoke event services tailored to your vision - complete with curated menus and elegant d&eacute;cor.</p>', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(8, 'home', 'events_image_url', 'image_url', 'https://shbmastercardworld.com.vn/wp-content/uploads/2024/08/15-scaled.jpg', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(9, 'home', 'diversity_heading', 'text', 'Diversity In Choices', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(10, 'home', 'diversity_text', 'html', '<p>From the coasts of the Mediterranean to the heart of Saigon - indulge in a menu crafted with culinary finesse and cultural soul.</p>', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(11, 'home', 'diversity_menu_link_text', 'text', 'Check Menu', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(12, 'home', 'diversity_menu_link_href', 'text', '/VINICA/menu', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(13, 'home', 'diversity_image_url', 'image_url', 'https://shbmastercardworld.com.vn/wp-content/uploads/2024/02/banner-detail-2.png', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(14, 'home', 'signature_dishes_heading', 'text', 'Our Signature Dishes', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(15, 'home', 'signature_dishes_view_all_text', 'text', 'View Full Menu', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(16, 'home', 'signature_dishes_view_all_href', 'text', '/VINICA/menu', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(17, 'home', 'our_story_image_url', 'image_url', 'https://media.istockphoto.com/id/153743372/photo/restaurant-table-and-chairs-with-place-settings1.jpg?s=612x612&w=0&k=20&c=jtyCnk3Rx3W1a5RZovPyO1k3BjTvFtCcp1RJzk1_m_k=', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(18, 'home', 'our_story_heading', 'text', 'Our Story', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(19, 'home', 'our_story_text', 'html', '<p>Founded with a deep passion for the rich tapestry of European culinary arts, VINICA was born from a desire to bring timeless charm and refined dining to the heart of the city. We believe that a meal is more than sustenance; it\'s an experience, a moment of joy, connection, and exquisite taste.</p>', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(20, 'home', 'our_story_learn_more_text', 'text', 'Learn More About Us', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(21, 'home', 'our_story_learn_more_href', 'text', '/VINICA/about', 0, 1, '2025-05-25 11:08:37', '2025-05-26 15:45:47'),
(22, 'about', 'about_hero_image_url', 'image_url', '/VINICA/layout/img/home_1.jpg', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(23, 'about', 'about_page_main_heading', 'text', 'In Our Restaurant', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(24, 'about', 'about_page_subtitle', 'html', '<p>More than just a restaurant, Vinica is a celebration of European culinary artistry and heartfelt hospitality.</p>', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(25, 'about', 'about_story_image_url', 'image_url', 'https://media.istockphoto.com/id/153743372/photo/restaurant-table-and-chairs-with-place-settings1.jpg?s=612x612&w=0&k=20&c=jtyCnk3Rx3W1a5RZovPyO1k3BjTvFtCcp1RJzk1_m_k=', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(26, 'about', 'about_story_heading', 'text', 'Our Story', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(27, 'about', 'about_story_text', 'html', '<p>Founded with a deep passion for the rich tapestry of European culinary arts, VINICA was born from a desire to bring timeless charm and refined dining to the heart of the city. We believe that a meal is more than sustenance; it\'s an experience, a moment of joy, connection, and exquisite taste.</p>\r\n<p>Our journey began with a vision to create a sanctuary where \"<em>A Taste of Europe, A Touch of Elegance</em>\" is not just a tagline, but the very essence of every dish served and every memory made. Our philosophy is rooted in crafting these unforgettable moments, echoing the warmth of European hospitality where every guest feels like cherished family.</p>', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(28, 'about', 'about_ambience_main_heading', 'text', 'Our Ambience', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(29, 'about', 'about_ambience_gh_image_url', 'image_url', 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(30, 'about', 'about_ambience_gh_name', 'text', 'The Grand Hall', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(31, 'about', 'about_ambience_gh_subtitle', 'text', 'Where Celebrations Come to Life', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(32, 'about', 'about_ambience_gh_text', 'html', '<p>Step into our Grand Hall and feel the grandeur of a European ballroom brought to life. With high ceilings, elegant chandeliers, and spacious layouts, this venue is designed for memorable celebrations - from refined corporate events to heartfelt weddings. Every corner whispers sophistication, setting the tone for unforgettable moments.</p>', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(33, 'about', 'about_ambience_tv_image_url', 'image_url', 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(34, 'about', 'about_ambience_tv_name', 'text', 'Terrace View', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(35, 'about', 'about_ambience_tv_subtitle', 'text', 'Dining Under the Stars', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(36, 'about', 'about_ambience_tv_text', 'html', '<p>Our rooftop terrace offers a stunning panoramic view of the city skyline, perfect for twilight dinners or cocktail evenings. Surrounded by gentle breezes and the glow of ambient lighting, the terrace becomes a serene escape, ideal for romantic dates or elevated private gatherings.</p>', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(37, 'about', 'about_ambience_ll_image_url', 'image_url', 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(38, 'about', 'about_ambience_ll_name', 'text', 'The Lobby Lounge', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(39, 'about', 'about_ambience_ll_subtitle', 'text', 'First Impressions of Elegance', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(40, 'about', 'about_ambience_ll_text', 'html', '<p>As you enter VINICA, you\'re welcomed by a lobby that blends modern refinement with classic European touches. Plush seating, floral arrangements, and curated art pieces create a space that invites guests to relax, converse, or enjoy a pre-dinner aperitif in style.</p>', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(41, 'about', 'about_ambience_pdr_image_url', 'image_url', 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(42, 'about', 'about_ambience_pdr_name', 'text', 'Private Dining Room', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(43, 'about', 'about_ambience_pdr_subtitle', 'text', 'Intimacy Meets Indulgence', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(44, 'about', 'about_ambience_pdr_text', 'html', '<p>For those seeking exclusivity, our private dining room provides an intimate setting tucked away from the bustle. With personalized service and a bespoke menu, it\'s perfect for business dinners, family celebrations, or romantic evenings that call for extra privacy.</p>', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(45, 'about', 'about_ambience_gc_image_url', 'image_url', 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(46, 'about', 'about_ambience_gc_name', 'text', 'Garden Courtyard', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(47, 'about', 'about_ambience_gc_subtitle', 'text', 'Tranquility in the Heart of the City', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(48, 'about', 'about_ambience_gc_text', 'html', '<p>Escape into our hidden garden courtyard, where nature meets fine dining. Surrounded by greenery, soft music, and subtle lighting, this space offers a tranquil retreat for brunches, tea hours, or alfresco dining under the stars.</p>', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(49, 'about', 'about_team_main_heading', 'text', 'Our Team', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(50, 'about', 'about_team_intro', 'html', '<p>At the heart of VINICA\'s exceptional experience is our dedicated team. Led by visionary chefs with a passion for European cuisine, our culinary artists meticulously craft each dish, blending traditional techniques with innovative flair. They are committed to sourcing the freshest, highest-quality ingredients to bring you \"Diversity In Choices\" and unparalleled flavors.</p>\r\n<p>Beyond the kitchen, our service staff embodies the spirit of genuine hospitality. Attentive, knowledgeable, and always warm, they strive to make every guest feel welcomed and cared for, ensuring your dining experience is seamless and delightful from start to finish. It\'s this collective passion and commitment that defines the VINICA touch.</p>', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(51, 'about', 'about_team_tm1_image_url', 'image_url', 'https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_640.png', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(52, 'about', 'about_team_tm1_name', 'text', 'Đinh Quốc Cường', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(53, 'about', 'about_team_tm1_role_or_id', 'text', '52300012', 0, 1, '2025-05-26 08:05:10', '2025-05-26 08:39:26'),
(54, 'about', 'about_team_tm2_image_url', 'image_url', 'https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_640.png', 0, 1, '2025-05-26 08:05:11', '2025-05-26 08:39:26'),
(55, 'about', 'about_team_tm2_name', 'text', 'Bùi Thị Bích Ngọc', 0, 1, '2025-05-26 08:05:11', '2025-05-26 08:39:26'),
(56, 'about', 'about_team_tm2_role_or_id', 'text', '52300228', 0, 1, '2025-05-26 08:05:11', '2025-05-26 08:39:26'),
(57, 'about', 'about_team_tm3_image_url', 'image_url', 'https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_640.png', 0, 1, '2025-05-26 08:05:11', '2025-05-26 08:39:26'),
(58, 'about', 'about_team_tm3_name', 'text', 'Huỳnh Thị Mỹ Ngọc', 0, 1, '2025-05-26 08:05:11', '2025-05-26 08:39:26'),
(59, 'about', 'about_team_tm3_role_or_id', 'text', '52300134', 0, 1, '2025-05-26 08:05:11', '2025-05-26 08:39:26'),
(63, 'about', 'about_vision_heading', 'text', 'Our Vision', 0, 1, '2025-05-26 08:32:20', '2025-05-26 08:32:27'),
(64, 'about', 'about_vision_text', 'html', '<p>To be the city\'s most beloved destination for European fine dining, renowned for exceptional cuisine, elegant ambiance, and heartfelt hospitality, where every visit is a cherished memory.</p>', 0, 1, '2025-05-26 08:32:20', '2025-05-26 08:32:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `page_items`
--

CREATE TABLE `page_items` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `type` enum('header','footer','logo') NOT NULL,
  `is_visible` tinyint(1) DEFAULT 1,
  `order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `page_items`
--

INSERT INTO `page_items` (`id`, `parent_id`, `name`, `slug`, `type`, `is_visible`, `order`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Home', 'home', 'header', 1, 1, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(2, NULL, 'About', 'about', 'header', 1, 2, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(3, NULL, 'Services', 'services', 'header', 1, 3, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(4, NULL, 'Menu', 'menu', 'header', 1, 4, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(5, NULL, 'Reservation', 'reservation', 'header', 1, 5, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(6, 3, 'Birthday Party', 'birthday-party', 'header', 1, 1, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(7, 3, 'Corporate Event', 'corporate-event', 'header', 1, 2, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(8, 3, 'Year-End Party', 'year-end-party', 'header', 1, 3, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(9, 3, 'Wedding', 'wedding', 'header', 1, 4, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(10, 3, 'Private Dining', 'private-dining', 'header', 1, 5, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(11, 4, 'Lunch Set', 'lunch-set', 'header', 1, 1, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(12, 4, 'Dinner Set', 'dinner-set', 'header', 1, 2, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(13, 4, 'À La Carte Menu', 'a-la-carte', 'header', 1, 3, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(14, 4, 'Party Menu', 'party-menu', 'header', 1, 4, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(15, 4, 'Buffet Menu', 'buffet', 'header', 1, 5, '2025-05-24 09:27:52', '2025-05-24 09:27:52'),
(16, NULL, 'ABOUT VINICA', NULL, 'footer', 1, 1, '2025-05-25 00:52:38', '2025-05-25 01:27:53'),
(17, NULL, 'SERVICE & DINING', NULL, 'footer', 1, 2, '2025-05-25 00:53:14', '2025-05-25 01:25:25'),
(18, NULL, 'SUPPORT', NULL, 'footer', 1, 3, '2025-05-25 00:53:43', '2025-05-25 01:25:35'),
(19, NULL, 'FOLLOW US', NULL, 'footer', 1, 4, '2025-05-25 00:54:05', '2025-05-25 01:25:43'),
(20, NULL, 'Explore Our Menu', 'menu', 'footer', 1, 0, '2025-05-25 00:55:30', '2025-05-25 00:55:30'),
(21, NULL, 'Make a Reservation', 'reservation', 'footer', 1, 1, '2025-05-25 00:56:38', '2025-05-25 00:56:38'),
(22, 16, 'Our Story', 'about/our-story', 'footer', 1, 1, '2025-05-25 00:52:38', '2025-05-25 01:35:48'),
(23, 16, 'Our Ambience', 'about/our-ambience', 'footer', 1, 2, '2025-05-25 00:52:38', '2025-05-25 01:36:04'),
(24, 16, 'Our Team', 'about/our-team', 'footer', 1, 3, '2025-05-25 00:52:38', '2025-05-25 01:36:16'),
(25, 17, 'Our Menu', 'menu', 'footer', 1, 1, '2025-05-25 00:52:38', '2025-05-25 01:35:48'),
(26, 17, 'Reservations', 'reservation', 'footer', 1, 2, '2025-05-25 00:52:38', '2025-05-25 01:35:48'),
(28, 18, 'Contact Us', 'contact-us', 'footer', 1, 2, '2025-05-25 00:52:38', '2025-05-25 01:35:48'),
(29, NULL, 'logo', '/VINICA/layout/img/logo.png', 'logo', 1, 0, '2025-05-25 01:47:28', '2025-05-25 01:53:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `recovery_tokens`
--

CREATE TABLE `recovery_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `recovery_tokens`
--

INSERT INTO `recovery_tokens` (`id`, `user_id`, `token`, `expires_at`, `used`) VALUES
(1, 5, '5cb59af118c4e4aa88392bc43fcd6773196608595c6df1034626bed86332aabd', '2025-05-25 18:39:21', 1),
(2, 2, 'b547ecfce8b3f7bd14d2381138471c34a45ea87c38b9c2a7dcd2c440974beb3f', '2025-05-26 23:39:51', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `service` varchar(50) DEFAULT NULL,
  `check_in_date` datetime NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reservations`
--

INSERT INTO `reservations` (`id`, `full_name`, `phone`, `email`, `service`, `check_in_date`, `area`, `status`, `created_at`, `updated_at`) VALUES
(1, 'abc', '0795987122', '52300121@student.tdtu.edu.vn', 'sjhd', '2025-05-27 00:00:00', '', 'pending', '2025-05-26 14:54:15', '2025-05-26 14:54:15'),
(2, 'abc', '0795987122', 'ngoc.acotax@gmail.com', '', '2025-05-28 00:00:00', '', 'pending', '2025-05-26 14:59:55', '2025-05-26 14:59:55'),
(3, 'Bùi Ngọc', '0902792649', 'cmoitruong796@gmail.com', 'Buffet', '2025-05-27 00:00:00', 'floor 2', 'pending', '2025-05-26 15:38:39', '2025-05-26 15:38:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `services`
--

INSERT INTO `services` (`id`, `name`, `slug`, `description`, `image_url`, `is_visible`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Birthday Party', 'birthday-party', '<p>Celebrate another year of life in style at VINICA. We offer a sophisticated backdrop for your birthday festivities, whether it\'s a milestone celebration or an intimate gathering with loved ones.</p>\r\n<ul>\r\n<li>Personalized menus to delight your guests.</li>\r\n<li>Elegant private or semi-private dining areas.</li>\r\n<li>Customizable d&eacute;cor and ambiance.</li>\r\n<li>Dedicated staff to ensure a seamless event.</li>\r\n</ul>', 'https://elegantlivingeveryday.com/wp-content/uploads/2023/10/How-to-Plan-an-Adult-Birthday-Party-Featured-Image.jpg', 1, 0, '2025-05-24 09:10:32', '2025-05-26 10:10:15'),
(2, 'Corporate Event', 'corporate-event', '<p>Impress your clients, reward your team, or host a distinguished business luncheon at VINICA. Our refined setting and professional service create the perfect atmosphere for successful corporate gatherings.</p><ul><li>Flexible spaces for meetings, presentations, or dinners.</li><li>Gourmet catering options, from canapés to full-course meals.</li><li>Audiovisual equipment assistance available.</li><li>Discreet and efficient service.</li></ul>', 'https://adamchristing.com/wp-content/uploads/2023/05/services-corporate-events-corporate-event-productive-brand-brand-brand-brand-employee-benefits-new-product-community-values-position-party-person-sense-space-important-part-connect.jpg', 1, 1, '2025-05-24 09:10:32', '2025-05-26 09:12:41'),
(3, 'Year-End Party', 'year-end-party', '<p>End the year on a high note with a memorable celebration at VINICA. Our elegant venue, exceptional cuisine, and festive atmosphere provide the ideal setting for your company\'s year-end festivities or a sophisticated gathering with friends.</p><ul><li>Themed décor and entertainment options.</li><li>Curated festive menus and beverage packages.</li><li>Spacious areas for mingling and celebration.</li><li>Attentive team to manage every detail.</li></ul>', 'https://livechannel.vn/wp-content/uploads/2023/11/z4835364751671_259b97ccfe4a4a0338b963228cef136e-1024x683.jpg', 1, 2, '2025-05-24 09:10:32', '2025-05-26 09:12:41'),
(4, 'Wedding', 'wedding', '<p>Celebrate your special day in an atmosphere of timeless elegance at VINICA. We offer an intimate and sophisticated venue for your wedding reception, rehearsal dinner, or engagement party, promising an unforgettable experience for you and your guests.</p><ul><li>Bespoke wedding menus crafted by our chefs.</li><li>Elegant spaces adaptable to your guest list.</li><li>Personalized service from our dedicated wedding coordinator.</li><li>Romantic ambiance and exquisite photo opportunities.</li></ul>', 'https://www.realweddings.com.au/media/images/44180488_10212947019490598_878694611625705472.width-4096.jpg', 1, 3, '2025-05-24 09:10:32', '2025-05-26 09:12:41'),
(5, 'Private Dining', 'private-dining', '<p>For moments that call for exclusivity and intimacy, VINICA offers elegant private dining rooms. Perfect for confidential business meetings, special family gatherings, or a VIP experience, our private spaces provide comfort, discretion, and our renowned culinary excellence.</p><ul><li>Beautifully appointed private and semi-private rooms.</li><li>Customizable menus to suit your preferences.</li><li>Personalized service and attention to detail.</li><li>Ideal for both social and corporate private events.</li></ul>', 'https://thehoxton.com/wp-content/uploads/sites/5/2020/06/TheHoxton_Summerly_March2019_Day2-336.jpg', 1, 4, '2025-05-24 09:10:32', '2025-05-26 09:12:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `email_verified`, `verification_token`, `created_at`, `updated_at`) VALUES
(2, 'ngocbui', '$2y$10$tUjESfcUPTHg8N96z/CvmuBzwH1IX1HjVOqYxzOPDvmWlSRp03/yG', 'btbn271@gmail.com', 'admin', 1, NULL, '2025-05-24 06:13:17', '2025-05-26 15:41:37'),
(5, 'ngoc271', '$2y$10$ZH3/fqKrurL/ITX6yFUDI.tOYP1SJxNjXP/.9J7UYce2MtZ8F0jvu', 'ngocbui27012109@gmail.com', 'staff', 1, NULL, '2025-05-25 10:33:37', '2025-05-25 23:53:35'),
(6, 'cuong', '$2y$10$JU5.9NRF9TF73Y/ENbak9.xz4F6Fc5VVsyokL5bWNNbAktktOlH8S', '52300228@student.tdtu.edu.vn', 'staff', 0, '62f9aa3529cd8f41e6564e9a6bca749702da591dcd56a16eabef8a06af206b5f', '2025-05-26 15:43:29', '2025-05-26 15:43:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `capacity` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `venues`
--

INSERT INTO `venues` (`id`, `service_id`, `name`, `capacity`, `description`, `image_url`, `is_visible`, `display_order`, `created_at`, `updated_at`) VALUES
(3, 2, 'The Boardroom', '8-12 guests', 'Formal, fully equipped for meetings.', 'https://example.com/boardroom.jpg', 1, 0, '2025-05-24 09:10:32', '2025-05-24 09:10:32'),
(4, 3, 'The Grand Ballroom', '150-250 guests', 'Majestic for large-scale events.', 'https://example.com/grand_ballroom.jpg', 1, 0, '2025-05-24 09:10:32', '2025-05-24 09:10:32'),
(5, 4, 'The Garden Courtyard', 'up to 70 guests', 'Picturesque outdoor setting.', 'https://example.com/garden_courtyard.jpg', 1, 0, '2025-05-24 09:10:32', '2025-05-24 09:10:32'),
(6, 1, 'The Ruby Private Room', '10-20 guests', 'Intimate & elegant, garden view, perfect for close gatherings and small celebrations.', 'https://www.saigon.park.hyattrestaurants.com/uploads/1/1/2/9/112964589/ballroom-longtable-setup-550x400_orig.jpg', 1, 0, '2025-05-26 09:12:41', '2025-05-26 10:35:27'),
(7, 1, 'The Sapphire Banquet Hall', '50-80 guests', 'Spacious, with stage, ideal for larger birthday celebrations and entertainment needs.', 'https://www.saigon.park.hyattrestaurants.com/uploads/1/1/2/9/112964589/phs-poolhouse-banquet-550x400_orig.jpg', 1, 1, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(8, 1, 'Rooftop Garden Terrace', 'up to 40 guests', 'Outdoor charm, city views, unique setting for casual and chic birthday parties.', 'https://rooftopgardens.co.uk/wp-content/uploads/2023/09/Gallery-3.jpg', 1, 2, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(9, 1, 'The Vinica Lounge', '20-35 guests', 'Stylish lounge area, comfortable seating, great for cocktail-style birthday mixers.', 'https://noithatkendesign.vn/storage/app/media/uploaded-files/Lounge-la-gi-2.png', 1, 3, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(10, 2, 'The Boardroom', '8-12 guests', 'Formal, fully equipped for executive meetings, quiet ambiance for focused discussions.', 'https://www.emeraldcruises.co.nz/-/media/project/scenic/emerald-cruises/river/river-experience/ship-experience/our-ships-southeast-asia/ecrc_ourshipsarc_dining-reflections.jpg?iar=0&rev=655ff087e7634bcfa4bea9895a51e40c&hash=4B5F4D923A4E4617F2490C421CDACBB', 1, 0, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(11, 2, 'Emerald Conference Room', '20-30 guests', 'Flexible setup, AV ready, ideal for workshops, training sessions & presentations.', 'https://thevendry.com/cdn-cgi/image/width=640,quality=75,fit=contain,metadata=none,format=auto/https%3A%2F%2Fs3.us-east-1.amazonaws.com%2Fuploads.thevendry.co%2F23050%2F1674203909155_Screen-Shot-2023-01-20-at-3.jpg', 1, 1, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(12, 2, 'The Sapphire Banquet Hall', 'up to 100 (theatre style)', 'Versatile for product launches, seminars, or company appreciation dinners.', 'https://www.saigon.park.hyattrestaurants.com/uploads/1/1/2/9/112964589/phs-poolhouse-banquet-550x400_orig.jpg', 1, 2, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(13, 2, 'The Grand Ballroom', '150-250 guests', 'Majestic and spacious, perfect for large conferences, galas, and award ceremonies.', 'https://file.hstatic.net/200000887901/file/mox92ds-0pv3f.jpg', 1, 3, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(14, 3, 'The Sapphire Banquet Hall', '50-80 guests (seated dinner)', 'Festive ambiance, dance floor space, excellent for company year-end celebrations.', 'https://www.saigon.park.hyattrestaurants.com/uploads/1/1/2/9/112964589/phs-poolhouse-banquet-550x400_orig.jpg', 1, 0, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(15, 3, 'Rooftop Garden Terrace', 'up to 50 (cocktail style)', 'Stunning night views, open-air, perfect for a chic and modern year-end bash.', 'https://rooftopgardens.co.uk/wp-content/uploads/2023/09/Gallery-3.jpg', 1, 1, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(16, 3, 'The Grand Ballroom', 'up to 200 (with dance floor)', 'Grandeur and elegance for large-scale company year-end galas and award nights.', 'https://file.hstatic.net/200000887901/file/mox92ds-0pv3f.jpg', 1, 2, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(17, 4, 'The Grand Ballroom', '100-180 guests (banquet style)', 'Timeless elegance, high ceilings, customizable for your perfect wedding reception.', 'https://file.hstatic.net/200000887901/file/mox92ds-0pv3f.jpg', 1, 0, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(18, 4, 'The Sapphire Banquet Hall', '40-60 guests', 'Charming and refined, perfect for intimate wedding ceremonies or receptions.', 'https://www.saigon.park.hyattrestaurants.com/uploads/1/1/2/9/112964589/phs-poolhouse-banquet-550x400_orig.jpg', 1, 1, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(19, 4, 'The Garden Courtyard', 'up to 70 (ceremony)', 'Picturesque outdoor setting for romantic wedding ceremonies or cocktail hours.', 'https://lh3.googleusercontent.com/CO8CgbECxPEXfKZ3OexquKLzacjDe1qgS53XcEpOqrC_NnhILeeyK_Y3TKssaXzgC0JcT8JepI46K7vbLoOCBp9q=w1082-h971-n-l80-e31', 1, 2, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(20, 5, 'The Ruby Private Room', '10-20 guests', 'Discreet and elegant, ideal for VIP dinners, family celebrations, or business meals.', 'https://www.saigon.park.hyattrestaurants.com/uploads/1/1/2/9/112964589/ballroom-longtable-setup-550x400_orig.jpg', 1, 0, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(21, 5, 'The Wine Cellar Room', '6-10 guests', 'Unique ambiance surrounded by fine wines, perfect for exclusive tastings and dinners.', 'https://www.hotelarras.com/images/1700-960/arras-cellars-235968cc.jpg', 1, 1, '2025-05-26 09:12:41', '2025-05-26 09:12:41'),
(22, 5, 'Chef\'s Table Experience', '4-8 guests', 'An immersive culinary journey with a bespoke menu, interacting directly with our chefs.', 'https://www.miasaigon.com/wp-content/uploads/2023/05/Chef-table-at-Emerald.jpg', 1, 2, '2025-05-26 09:12:41', '2025-05-26 09:12:41');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_page_section_item_order` (`page_slug`,`section_key`,`item_order`);

--
-- Chỉ mục cho bảng `page_items`
--
ALTER TABLE `page_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `recovery_tokens`
--
ALTER TABLE `recovery_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT cho bảng `page_items`
--
ALTER TABLE `page_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `recovery_tokens`
--
ALTER TABLE `recovery_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `fk_menu_item_category` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `page_items`
--
ALTER TABLE `page_items`
  ADD CONSTRAINT `page_items_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `page_items` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `venues`
--
ALTER TABLE `venues`
  ADD CONSTRAINT `venues_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
