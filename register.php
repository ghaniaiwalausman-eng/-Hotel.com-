// pages/register.php
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        $error = "Email already registered";
    } else {
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['user_name'] = $username;
            $_SESSION['user_role'] = 'user';
            header('Location: index.php?page=dashboard');
            exit();
        } else {
            $error = "Registration failed";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Moonlight Serenity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-5xl bg-white/30 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white/40" data-aos="fade-up">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left side - Branding -->
                <div class="bg-gradient-to-br from-indigo-900 to-purple-900 text-white rounded-2xl p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-3xl font-serif font-bold">
                            <i class="fas fa-moon text-4xl"></i>
                            <span>moonlight</span>
                        </div>
                        <div class="h-0.5 w-20 bg-amber-300 my-4"></div>
                        <p class="text-lg font-light italic">"Start your journey with us"</p>
                        <div class="mt-8 space-y-4">
                            <div class="flex gap-3"><i class="fas fa-circle-check text-amber-300"></i> <span>Member exclusive deals</span></div>
                            <div class="flex gap-3"><i class="fas fa-gem text-amber-300"></i> <span>Earn loyalty points</span></div>
                            <div class="flex gap-3"><i class="fas fa-headset text-amber-300"></i> <span>Priority support</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- Right side - Registration Form -->
                <div class="p-6">
                    <h2 class="text-3xl font-serif font-bold text-gray-800">Create Account</h2>
                    <p class="text-gray-500 mt-2">Join our community of travelers</p>
                    
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="mt-8 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="username" required 
                                   class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-300 outline-none transition"
                                   placeholder="Alex Morgan">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" required 
                                   class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-300 outline-none transition"
                                   placeholder="alex@example.com">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" required 
                                   class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-300 outline-none transition"
                                   placeholder="•••••••• (min. 8 characters)">
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-indigo-700 hover:bg-indigo-800 text-white font-semibold py-3.5 rounded-xl shadow-md transition transform hover:scale-[1.02]">
                            Create Account
                        </button>
                    </form>
                    
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">or sign up with</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 justify-center">
                        <button onclick="socialLogin('google')" class="bg-gray-100 hover:bg-gray-200 p-3 rounded-full w-12 h-12 flex items-center justify-center text-xl">
                            <i class="fab fa-google text-red-500"></i>
                        </button>
                        <button onclick="socialLogin('facebook')" class="bg-gray-100 hover:bg-gray-200 p-3 rounded-full w-12 h-12 flex items-center justify-center text-xl">
                            <i class="fab fa-facebook-f text-blue-700"></i>
                        </button>
                    </div>
                    
                    <p class="text-center mt-8 text-gray-600">
                        Already have an account? 
                        <a href="index.php?page=login" class="text-indigo-700 font-semibold hover:underline">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800 });
        
        function socialLogin(provider) {
            window.location.href = `index.php?page=dashboard&social=${provider}`;
        }
    </script>
</body>
</html>
