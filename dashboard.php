// pages/hotel-details.php
<?php
$hotel_id = $_GET['id'] ?? 0;
$hotel = getHotelById($hotel_id);

if (!$hotel) {
    header('Location: index.php?page=dashboard');
    exit();
}

$is_favorite = isLoggedIn() ? isFavorite($_SESSION['user_id'], $hotel_id) : false;
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <button onclick="window.location.href='index.php?page=dashboard'" 
            class="mb-6 text-indigo-600 hover:text-indigo-800 transition flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Back to Hotels
    </button>
    
    <!-- Hotel Details -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden" data-aos="fade-up">
        <!-- Gallery -->
        <div class="grid grid-cols-4 gap-2 p-3 bg-gray-100">
            <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" 
                 class="rounded-xl h-32 object-cover col-span-2">
            <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=400" 
                 class="rounded-xl h-32 object-cover">
            <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=400" 
                 class="rounded-xl h-32 object-cover">
            <img src="https://images.unsplash.com/photo-1568495248636-6432b97bd949?w=400" 
                 class="rounded-xl h-32 object-cover col-span-2">
            <img src="https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=400" 
                 class="rounded-xl h-32 object-cover">
        </div>
        
        <!-- Content -->
        <div class="p-8 grid md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <div class="flex justify-between items-start">
                    <h1 class="text-3xl font-serif font-bold"><?php echo htmlspecialchars($hotel['name']); ?></h1>
                    <button onclick="toggleFavorite(<?php echo $hotel['id']; ?>)" 
                            class="text-2xl <?php echo $is_favorite ? 'text-rose-500' : 'text-gray-400'; ?> hover:text-rose-500 transition">
                        <i class="<?php echo $is_favorite ? 'fas' : 'far'; ?> fa-heart"></i>
                    </button>
                </div>
                <p class="text-gray-500 mt-2">
                    <i class="fas fa-map-pin mr-1"></i><?php echo htmlspecialchars($hotel['location']); ?>
                </p>
                <div class="flex items-center my-3">
                    <span class="bg-emerald-700 text-white px-3 py-1 rounded-full flex items-center">
                        <?php echo $hotel['rating']; ?> <i class="fas fa-star ml-1 text-xs"></i>
                    </span>
                    <span class="ml-3 text-gray-500">(1,234 reviews)</span>
                </div>
                
                <p class="text-gray-700 my-4"><?php echo htmlspecialchars($hotel['description']); ?></p>
                
                <div class="border-t pt-6">
                    <h3 class="font-semibold text-lg mb-3">Amenities</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php 
                        $amenities = explode(',', $hotel['amenities']);
                        foreach ($amenities as $amenity): 
                        ?>
                        <span class="bg-gray-100 px-4 py-2 rounded-full text-sm"><?php echo trim($amenity); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="border-t mt-6 pt-6">
                    <h3 class="font-semibold text-lg mb-3">Room Types</h3>
                    <div class="flex gap-3">
                        <span class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-full text-sm">Deluxe King</span>
                        <span class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-full text-sm">Twin</span>
                        <span class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-full text-sm">Suite</span>
                    </div>
                </div>
                
                <!-- Reviews -->
                <div class="border-t mt-6 pt-6">
                    <h3 class="font-semibold text-lg mb-3">Guest Reviews</h3>
                    <div class="space-y-3">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium">Emily R.</span>
                                <span class="text-yellow-400">★★★★★</span>
                            </div>
                            <p class="text-sm text-gray-600">"Absolutely stunning property with amazing views!"</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium">James K.</span>
                                <span class="text-yellow-400">★★★★★</span>
                            </div>
                            <p class="text-sm text-gray-600">"Impeccable service and beautiful rooms."</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Booking Card -->
            <div class="bg-indigo-50 p-6 rounded-2xl border shadow-lg h-fit sticky top-24">
                <div class="text-3xl font-bold text-indigo-800">
                    $<?php echo number_format($hotel['price'], 2); ?> 
                    <span class="text-sm font-normal text-gray-500">/night</span>
                </div>
                
                <form id="bookingForm" onsubmit="bookHotel(event, <?php echo $hotel['id']; ?>)">
                    <div class="my-4">
                        <label class="text-sm font-medium block mb-2">Check-in</label>
                        <input type="date" id="checkin" required 
                               class="w-full rounded-lg border p-3 focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    
                    <div class="my-4">
                        <label class="text-sm font-medium block mb-2">Check-out</label>
                        <input type="date" id="checkout" required 
                               class="w-full rounded-lg border p-3 focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    
                    <div class="my-4">
                        <label class="text-sm font-medium block mb-2">Guests</label>
                        <select id="guests" class="w-full rounded-lg border p-3">
                            <option value="1">1 Guest</option>
                            <option value="2">2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                        </select>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-4 rounded-xl font-semibold text-lg transition transform hover:scale-[1.02]">
                        Reserve Now
                    </button>
                </form>
                
                <p class="text-xs text-center mt-4 text-gray-600">
                    <i class="far fa-credit-card mr-1"></i>Pay later · Free cancellation
                </p>
            </div>
        </div>
        
        <!-- Map -->
        <div class="h-48 bg-gray-300 flex items-center justify-center text-gray-600 border-t">
            <i class="fas fa-map-marker-alt mr-2"></i> Interactive Map Location
        </div>
    </div>
</div>

<script>
function toggleFavorite(hotelId) {
    fetch('ajax/toggle-favorite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'hotel_id=' + hotelId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function bookHotel(event, hotelId) {
    event.preventDefault();
    
    const checkin = document.getElementById('checkin').value;
    const checkout = document.getElementById('checkout').value;
    const guests = document.getElementById('guests').value;
    
    if (!checkin || !checkout) {
        showToast('Please select dates');
        return;
    }
    
    fetch('ajax/create-booking.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `hotel_id=${hotelId}&check_in=${checkin}&check_out=${checkout}&guests=${guests}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Booking confirmed! Check your email.');
            setTimeout(() => {
                window.location.href = 'index.php?page=bookings';
            }, 1500);
        } else {
            showToast('Booking failed. Please try again.');
        }
    });
}
</script>
