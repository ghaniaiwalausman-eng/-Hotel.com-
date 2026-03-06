// index.php - Main entry point
<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Page routing
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Check if user is logged in for protected pages
$protected_pages = ['dashboard', 'profile', 'bookings', 'favorites', 'admin'];
if (in_array($page, $protected_pages) && !isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}

// Admin only pages
$admin_pages = ['admin'];
if (in_array($page, $admin_pages) && (!isLoggedIn() || $_SESSION['user_role'] !== 'admin')) {
    header('Location: index.php?page=dashboard');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moonlight Serenity - Luxury Hotel Booking</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="antialiased bg-gray-50">
    <?php
    // Include header if not login/register page
    if (!in_array($page, ['login', 'register', 'reset-password'])) {
        include 'includes/header.php';
    }

    // Page routing
    switch($page) {
        case 'login':
            include 'pages/login.php';
            break;
        case 'register':
            include 'pages/register.php';
            break;
        case 'reset-password':
            include 'pages/reset-password.php';
            break;
        case 'dashboard':
            include 'pages/dashboard.php';
            break;
        case 'hotel-details':
            include 'pages/hotel-details.php';
            break;
        case 'profile':
            include 'pages/profile.php';
            break;
        case 'bookings':
            include 'pages/bookings.php';
            break;
        case 'favorites':
            include 'pages/favorites.php';
            break;
        case 'admin':
            include 'pages/admin/dashboard.php';
            break;
        case 'admin-hotels':
            include 'pages/admin/hotels.php';
            break;
        case 'admin-users':
            include 'pages/admin/users.php';
            break;
        case 'admin-bookings':
            include 'pages/admin/bookings.php';
            break;
        case 'about':
            include 'pages/about.php';
            break;
        case 'contact':
            include 'pages/contact.php';
            break;
        case 'faq':
            include 'pages/faq.php';
            break;
        default:
            include 'pages/home.php';
    }

    // Include footer if not login/register page
    if (!in_array($page, ['login', 'register', 'reset-password'])) {
        include 'includes/footer.php';
    }
    ?>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-8 right-8 bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-2xl z-50 flex items-center gap-3 transition-all duration-300 translate-y-0 opacity-0 pointer-events-none">
        <i class="fas fa-check-circle text-emerald-400 text-xl"></i>
        <span id="toastMsg">Notification</span>
    </div>

    <!-- Chat Support -->
    <div class="fixed bottom-7 right-7 z-40 flex flex-col items-end">
        <div id="chatPopup" class="hidden bg-white w-72 p-4 rounded-2xl mb-3 shadow-2xl border">
            <p class="text-sm font-medium">👋 How can we help?</p>
            <input class="w-full mt-2 p-2 rounded border" placeholder="Type your message...">
        </div>
        <button onclick="toggleChat()" class="bg-indigo-700 text-white w-16 h-16 rounded-full shadow-2xl text-3xl flex items-center justify-center hover:bg-indigo-800 transition">
            <i class="fas fa-comment-dots"></i>
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
