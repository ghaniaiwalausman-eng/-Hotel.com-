-- database.sql - Complete database schema
CREATE DATABASE IF NOT EXISTS moonlight_db;
USE moonlight_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hotels table
CREATE TABLE hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    price DECIMAL(10,2),
    rating DECIMAL(2,1) DEFAULT 0,
    image_url VARCHAR(500),
    amenities TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    hotel_id INT,
    check_in DATE,
    check_out DATE,
    guests INT,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
);

-- Favorites table
CREATE TABLE favorites (
    user_id INT,
    hotel_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, hotel_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
);

-- Messages/Chat table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    is_admin_reply BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample hotels
INSERT INTO hotels (name, description, location, price, rating, image_url, amenities) VALUES
('Skyline NYC', 'Luxury hotel with stunning Manhattan views and world-class amenities', 'New York', 299, 4.8, 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=500', 'Pool, Spa, Restaurant, Gym, WiFi'),
('Coastal Retreat', 'Beachfront paradise with private access and ocean views', 'Malibu', 189, 4.6, 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=500', 'Beach access, Pool, Bar, WiFi, Breakfast'),
('Alpine Lodge', 'Ski-in/ski-out luxury chalet in the heart of the Alps', 'Swiss Alps', 420, 4.9, 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=500', 'Ski rental, Spa, Fireplace, Restaurant, WiFi'),
('Sunset Resort', 'Infinity pool, world-class spa, and stunning sunsets', 'Bali', 350, 4.7, 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=500', 'Infinity pool, Yoga, Restaurant, Spa, WiFi'),
('Downtown Loft', 'Industrial style loft in city center with rooftop bar', 'Berlin', 210, 4.3, 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=500', 'Gym, Rooftop bar, WiFi, Kitchen'),
('Ocean Breeze', 'Miami Beach oceanfront with vibrant nightlife', 'Miami', 275, 4.5, 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=500', 'Pool, Beach club, Bar, WiFi, Breakfast'),
('Royal Palm', 'Historic luxury hotel with modern amenities', 'London', 450, 4.9, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500', 'Spa, Restaurant, Pool, Gym, WiFi, Butler'),
('Desert Oasis', 'Luxury resort in the heart of the desert', 'Dubai', 520, 4.8, 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=500', 'Pool, Spa, Restaurant, Golf, WiFi');

-- Create admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('Admin', 'admin@moonlight.com', '$2y$10$YourHashedPasswordHere', 'admin');

-- Create sample regular user (password: user123)
INSERT INTO users (username, email, password, role) VALUES
('John Doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'user');
