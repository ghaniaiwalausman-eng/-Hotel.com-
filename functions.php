// includes/functions.php
<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserById($id) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getHotels($filters = []) {
    $database = new Database();
    $db = $database->getConnection();
    
    $sql = "SELECT * FROM hotels WHERE 1=1";
    $params = [];
    
    if (!empty($filters['search'])) {
        $sql .= " AND (name LIKE ? OR location LIKE ?)";
        $search = "%{$filters['search']}%";
        $params[] = $search;
        $params[] = $search;
    }
    
    if (!empty($filters['price_min'])) {
        $sql .= " AND price >= ?";
        $params[] = $filters['price_min'];
    }
    
    if (!empty($filters['price_max'])) {
        $sql .= " AND price <= ?";
        $params[] = $filters['price_max'];
    }
    
    if (!empty($filters['rating'])) {
        $sql .= " AND rating >= ?";
        $params[] = $filters['rating'];
    }
    
    $sql .= " ORDER BY rating DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getHotelById($id) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createBooking($user_id, $hotel_id, $check_in, $check_out, $guests, $total_price) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("INSERT INTO bookings (user_id, hotel_id, check_in, check_out, guests, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$user_id, $hotel_id, $check_in, $check_out, $guests, $total_price]);
}

function getUserBookings($user_id) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("
        SELECT b.*, h.name as hotel_name, h.image_url 
        FROM bookings b 
        JOIN hotels h ON b.hotel_id = h.id 
        WHERE b.user_id = ? 
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function toggleFavorite($user_id, $hotel_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if already favorite
    $stmt = $db->prepare("SELECT * FROM favorites WHERE user_id = ? AND hotel_id = ?");
    $stmt->execute([$user_id, $hotel_id]);
    
    if ($stmt->rowCount() > 0) {
        // Remove from favorites
        $stmt = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND hotel_id = ?");
        return $stmt->execute([$user_id, $hotel_id]) ? 'removed' : false;
    } else {
        // Add to favorites
        $stmt = $db->prepare("INSERT INTO favorites (user_id, hotel_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $hotel_id]) ? 'added' : false;
    }
}

function getUserFavorites($user_id) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("
        SELECT h.* FROM hotels h 
        JOIN favorites f ON h.id = f.hotel_id 
        WHERE f.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function isFavorite($user_id, $hotel_id) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("SELECT * FROM favorites WHERE user_id = ? AND hotel_id = ?");
    $stmt->execute([$user_id, $hotel_id]);
    return $stmt->rowCount() > 0;
}

function updateUserProfile($user_id, $data) {
    $database = new Database();
    $db = $database->getConnection();
    
    $sql = "UPDATE users SET ";
    $fields = [];
    $params = [];
    
    if (!empty($data['username'])) {
        $fields[] = "username = ?";
        $params[] = $data['username'];
    }
    if (!empty($data['email'])) {
        $fields[] = "email = ?";
        $params[] = $data['email'];
    }
    if (!empty($data['phone'])) {
        $fields[] = "phone = ?";
        $params[] = $data['phone'];
    }
    if (!empty($data['profile_image'])) {
        $fields[] = "profile_image = ?";
        $params[] = $data['profile_image'];
    }
    
    if (empty($fields)) return false;
    
    $sql .= implode(", ", $fields);
    $sql .= " WHERE id = ?";
    $params[] = $user_id;
    
    $stmt = $db->prepare($sql);
    return $stmt->execute($params);
}

function uploadImage($file) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $extension;
    $target_file = $target_dir . $filename;
    
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($extension, $allowed_types)) {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    return false;
}

function getAdminStats() {
    $database = new Database();
    $db = $database->getConnection();
    
    $stats = [];
    
    // Total hotels
    $stmt = $db->query("SELECT COUNT(*) FROM hotels");
    $stats['hotels'] = $stmt->fetchColumn();
    
    // Total users
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    $stats['users'] = $stmt->fetchColumn();
    
    // Total bookings
    $stmt = $db->query("SELECT COUNT(*) FROM bookings");
    $stats['bookings'] = $stmt->fetchColumn();
    
    // Total revenue
    $stmt = $db->query("SELECT SUM(total_price) FROM bookings WHERE status = 'confirmed'");
    $stats['revenue'] = $stmt->fetchColumn() ?: 0;
    
    return $stats;
}

function getAllHotels() {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->query("SELECT * FROM hotels ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllUsers() {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->query("SELECT id, username, email, phone, role, created_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllBookings() {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->query("
        SELECT b.*, u.username, h.name as hotel_name 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        JOIN hotels h ON b.hotel_id = h.id 
        ORDER BY b.created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addHotel($data) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("
        INSERT INTO hotels (name, description, location, price, rating, image_url, amenities) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['location'],
        $data['price'],
        $data['rating'],
        $data['image_url'],
        $data['amenities']
    ]);
}

function updateHotel($id, $data) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("
        UPDATE hotels 
        SET name = ?, description = ?, location = ?, price = ?, rating = ?, image_url = ?, amenities = ? 
        WHERE id = ?
    ");
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['location'],
        $data['price'],
        $data['rating'],
        $data['image_url'],
        $data['amenities'],
        $id
    ]);
}

function deleteHotel($id) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("DELETE FROM hotels WHERE id = ?");
    return $stmt->execute([$id]);
}

function updateBookingStatus($id, $status) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}
?>
