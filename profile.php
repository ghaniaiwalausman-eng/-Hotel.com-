// pages/profile.php
<?php
$user = getUserById($_SESSION['user_id']);
$bookings = getUserBookings($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_data = [];
    
    if (!empty($_POST['username'])) {
        $update_data['username'] = $_POST['username'];
    }
    if (!empty($_POST['email'])) {
        $update_data['email'] = $_POST['email'];
    }
    if (!empty($_POST['phone'])) {
        $update_data['phone'] = $_POST['phone'];
    }
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image_path = uploadImage($_FILES['profile_image']);
        if ($image_path) {
            $update_data['profile_image'] = $image_path;
        }
    }
    
    if (!empty($update_data)) {
        updateUserProfile($_SESSION['user_id'], $update_data);
        $user = getUserById($_SESSION['user_id']);
        $success = "Profile updated successfully";
    }
}
?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Profile Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-serif font-bold text-gray-800">My Profile</h1>
        <p class="text-gray-500 mt-1">Manage your account settings</p>
    </div>
    
    <?php if (isset($success)): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $success; ?>
    </div>
    <?php endif; ?>
    
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl p-8" data-aos="fade-up">
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <!-- Profile Image -->
            <div class="flex items-center gap-6">
                <div class="relative">
                    <?php if ($user['profile_image']): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                             class="w-28 h-28 rounded-full object-cover border-4 border-indigo-100">
                    <?php else: ?>
                        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-4xl border-4 border-white shadow-xl">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <label for="profile_image" class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-full cursor-pointer hover:bg-indigo-700 transition">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*">
                </div>
                <div>
                    <h3 class="text-2xl font-semibold"><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p class="text-gray-500">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
            
            <!-- Profile Fields -->
            <div class="grid md:grid-cols-2 gap-6 pt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-300 outline-none"
                           placeholder="+1 234 567 8900">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" value="New York, USA" readonly disabled 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50">
                </div>
            </div>
            
            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white px-8 py-3 rounded-xl font-medium transition">
                    Save Changes
                </button>
                <a href="index.php?page=dashboard" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-3 rounded-xl font-medium transition">
                    Cancel
                </a>
            </div>
        </form>
        
        <!-- Booking History -->
        <div class="border-t mt-8 pt-8">
            <h3 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-history text-indigo-600"></i> Booking History
            </h3>
            
            <?php if (empty($bookings)): ?>
                <p class="text-gray-500 text-center py-4">No bookings yet</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($bookings as $booking): ?>
                    <div class="bg-gray-50 rounded-xl p-4 flex justify-between items-center">
                        <div>
                            <h4 class="font-semibold"><?php echo htmlspecialchars($booking['hotel_name']); ?></h4>
                            <p class="text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($booking['check_in'])); ?> - 
                                <?php echo date('M d, Y', strtotime($booking['check_out'])); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold">$<?php echo number_format($booking['total_price'], 2); ?></span>
                            <span class="inline-block ml-3 px-3 py-1 rounded-full text-xs 
                                <?php echo $booking['status'] == 'confirmed' ? 'bg-green-100 text-green-700' : 
                                              ($booking['status'] == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'); ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
