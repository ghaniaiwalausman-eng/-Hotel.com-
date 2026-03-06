// includes/header.php
<?php
$current_page = $_GET['page'] ?? 'home';
$user = getUserById($_SESSION['user_id'] ?? 0);
?>
<header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 border-b border-gray-200">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-2 text-2xl font-serif font-bold text-gray-800">
                <i class="fas fa-moon text-indigo-600 text-3xl"></i>
                <span>moonlight</span>
            </a>
            
            <!-- Main Navigation -->
            <div class="hidden md:flex items-center gap-8">
                <a href="index.php" class="<?php echo $current_page == 'home' ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600'; ?> transition">
                    Home
                </a>
                <a href="index.php?page=dashboard" class="<?php echo $current_page == 'dashboard' ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600'; ?> transition">
                    Hotels
                </a>
                <a href="index.php?page=about" class="<?php echo $current_page == 'about' ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600'; ?> transition">
                    About
                </a>
                <a href="index.php?page=contact" class="<?php echo $current_page == 'contact' ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600'; ?> transition">
                    Contact
                </a>
                <a href="index.php?page=faq" class="<?php echo $current_page == 'faq' ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600'; ?> transition">
                    FAQ
                </a>
            </div>
            
            <!-- User Menu -->
            <div class="flex items-center gap-4">
                <?php if (isLoggedIn()): ?>
                    <a href="index.php?page=bookings" class="relative text-gray-600 hover:text-indigo-600">
                        <i class="far fa-calendar-check text-xl"></i>
                    </a>
                    <a href="index.php?page=favorites" class="relative text-gray-600 hover:text-indigo-600">
                        <i class="far fa-heart text-xl"></i>
                    </a>
                    <div class="relative">
                        <button onclick="toggleUserMenu()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 pl-2 pr-4 py-1.5 rounded-full transition">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white">
                                <?php if ($user && $user['profile_image']): ?>
                                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" class="w-8 h-8 rounded-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-user text-sm"></i>
                                <?php endif; ?>
                            </div>
                            <span class="font-medium text-sm hidden sm:inline"><?php echo htmlspecialchars($user['username'] ?? 'User'); ?></span>
                            <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                        </button>
                        
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border py-2 z-40">
                            <a href="index.php?page=profile" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50">
                                <i class="fas fa-user w-5 text-gray-500"></i> Profile
                            </a>
                            <a href="index.php?page=bookings" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50">
                                <i class="fas fa-calendar-check w-5 text-gray-500"></i> My Trips
                            </a>
                            <a href="index.php?page=favorites" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50">
                                <i class="fas fa-heart w-5 text-gray-500"></i> Favorites
                            </a>
                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <div class="border-t my-1"></div>
                            <a href="index.php?page=admin" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-indigo-600">
                                <i class="fas fa-shield-alt w-5"></i> Admin Panel
                            </a>
                            <?php endif; ?>
                            <div class="border-t my-1"></div>
                            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 hover:bg-red-50 text-red-600">
                                <i class="fas fa-sign-out-alt w-5"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="index.php?page=login" class="text-gray-600 hover:text-indigo-600 transition">Login</a>
                    <a href="index.php?page=register" class="bg-indigo-600 text-white px-5 py-2 rounded-full hover:bg-indigo-700 transition">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<script>
function toggleUserMenu() {
    document.getElementById('userMenu').classList.toggle('hidden');
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('userMenu');
    const button = event.target.closest('button');
    if (!button && menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
    }
});
</script>
