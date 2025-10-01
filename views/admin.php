<?php
session_start();
require_once '../../DAO/pages.php';
$userDAO = new UserDAO();
$user = $userDAO->findById($_SESSION['user_id']);

if (!isset($_SESSION['user_id'])) {
    header("Location: /VINICA/login");
    exit;
} elseif ($user['role'] !== 'admin') {
    header("Location: /VINICA/login");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_slug = $_POST['page_slug'] ?? 'home';
    $content = $_POST['content'] ?? '';
    update_page_content($page_slug, $content);
    $success = 'Content updated successfully!';
}

// Get available pages from nav_items
$nav_items = get_navbar_items();
$page_slugs = [];
foreach ($nav_items as $item) {
    $page_slugs[] = $item['slug'];
    if (!empty($item['children'])) {
        foreach ($item['children'] as $child) {
            $page_slugs[] = $item['slug'] . '/' . $child['slug'];
        }
    }
}

$current_slug = $_GET['page'] ?? 'home';
$content = get_page_content($current_slug);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VINICA - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/VINICA/layout/css/styles.css">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>VINICA Admin Panel</h1>
        <a href="/VINICA/logout" class="btn btn-secondary mb-3">Logout</a>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="/VINICA/admin">
            <div class="mb-3">
                <label for="page_slug" class="form-label">Select Page</label>
                <select name="page_slug" id="page_slug" class="form-select" onchange="window.location.href='/VINICA/admin?page='+this.value">
                    <?php foreach ($page_slugs as $slug): ?>
                        <option value="<?php echo htmlspecialchars($slug); ?>" <?php echo $slug === $current_slug ? 'selected' : ''; ?>><?php echo htmlspecialchars($slug); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea name="content" id="content" class="form-control"><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        CKEDITOR.replace('content');
    </script>
</body>
</html>