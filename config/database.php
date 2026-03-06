// config/database.php
<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'moonlight_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

// Create tables if not exists
function initializeDatabase() {
    $database = new Database();
    $db = $database->getConnection();
    
    // Users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        profile_image VARCHAR(255),
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Hotels table
    $db->exec("CREATE TABLE IF NOT EXISTS hotels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        location VARCHAR(255),
        price DECIMAL(10,2),
        rating DECIMAL(2,1),
        image_url VARCHAR(500),
        amenities TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Bookings table
    $db->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        hotel_id INT,
        check_in DATE,
        check_out DATE,
        guests INT,
        total_price DECIMAL(10,2),
        status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (hotel_id) REFERENCES hotels(id)
    )");

    // Favorites table
    $db->exec("CREATE TABLE IF NOT EXISTS favorites (
        user_id INT,
        hotel_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, hotel_id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (hotel_id) REFERENCES hotels(id)
    )");

    // Messages table for chat
    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        message TEXT,
        is_admin_reply BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Insert sample hotels if table empty
    $stmt = $db->query("SELECT COUNT(*) FROM hotels");
    if ($stmt->fetchColumn() == 0) {
        $sample_hotels = [
            ['Skyline NYC', 'Luxury hotel with stunning Manhattan views', 'New York', 299, 4.8, 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=500', 'Pool, Spa, Restaurant'],
            ['Coastal Retreat', 'Beachfront paradise with private access', 'Malibu', 189, 4.6, 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=500', 'Beach access, Pool, Bar'],
            ['Alpine Lodge', 'Ski-in/ski-out luxury chalet', 'Swiss Alps', 420, 4.9, 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=500', 'Ski rental, Spa, Fireplace'],
            ['Sunset Resort', 'Infinity pool & world-class spa', 'Bali', 350, 4.7, 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=500', 'Infinity pool, Yoga, Restaurant'],
            ['Downtown Loft', 'Industrial style in city center', 'Berlin', 210, 4.3, 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=500', 'Gym, Rooftop bar'],
            ['Ocean Breeze', 'Miami Beach oceanfront', 'Miami', 275, 4.5, 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=500', 'Pool, Beach club']
        ];

        $stmt = $db->prepare("INSERT INTO hotels (name, description, location, price, rating, image_url, amenities) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($sample_hotels as $hotel) {
            $stmt->execute($hotel);
        }
    }

    // Create admin user if not exists
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role='admin'");
    if ($stmt->fetchColumn() == 0) {
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $db->exec("INSERT INTO users (username, email, password, role) VALUES ('Admin', 'admin@moonlight.com', '$admin_password', 'admin')");
    }
}

// Initialize database on first run
initializeDatabase();
?>
