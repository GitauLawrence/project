-- Create database
CREATE DATABASE IF NOT EXISTS sellam_real_estate;
USE sellam_real_estate;

-- Create admin_users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT IGNORE INTO admin_users (username, password, email) VALUES
('admin', '$2y$10$H2O2Bjgh0qGMtLQ.Wv1xQuSk16VnRDL6/Ygd5NXAXf.XpJqRjZ6Ke', 'admin@sellam.co.ke');

-- Create locations table
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample locations
INSERT IGNORE INTO locations (name) VALUES
('Karen'), ('Westlands'), ('Kilimani'), ('Lavington'), ('Runda'),
('Muthaiga'), ('Kileleshwa'), ('Upper Hill'), ('Gigiri'), ('Kitisuru');

-- Create amenities table
CREATE TABLE IF NOT EXISTS amenities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample amenities
INSERT IGNORE INTO amenities (name) VALUES
('Swimming Pool'), ('Gym'), ('Security'), ('Parking'), ('Garden'),
('Balcony'), ('Air Conditioning'), ('Internet/WiFi'), ('Water Storage'),
('Backup Generator'), ('Servant Quarter'), ('CCTV'), ('Elevator'),
('Furnished'), ('Electric Fence');

-- Create properties table
CREATE TABLE IF NOT EXISTS properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type ENUM('residential', 'commercial') NOT NULL,
    purpose ENUM('buy', 'rent') NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    bedrooms INT DEFAULT 0,
    bathrooms INT DEFAULT 0,
    area DECIMAL(10, 2) NOT NULL,
    garages INT DEFAULT 0,
    location_id INT NOT NULL,
    year_built INT,
    description TEXT NOT NULL,
    main_image VARCHAR(255) NOT NULL,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id)
);

-- Create property_images table
CREATE TABLE IF NOT EXISTS property_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Create property_amenities table
CREATE TABLE IF NOT EXISTS property_amenities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    amenity_id INT NOT NULL,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE CASCADE,
    UNIQUE KEY property_amenity (property_id, amenity_id)
);

-- Create inquiries table
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
);

-- Create contact_messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample properties (Make sure location IDs are accurate)
-- You may need to update location_id values if they change in your DB
INSERT IGNORE INTO properties (id, title, type, purpose, price, bedrooms, bathrooms, area, garages, location_id, year_built, description, main_image, featured) VALUES
(1, 'Luxurious 4 Bedroom Villa in Karen', 'residential', 'buy', 45000000, 4, 3, 350, 2, 1, 2020, 'Luxurious 4 bedroom villa located in the serene environment of Karen...', '../assets/images/properties/villa1.jpg', 1),
(2, 'Modern 3 Bedroom Apartment in Kilimani', 'residential', 'rent', 150000, 3, 2, 180, 1, 3, 2019, 'Beautiful 3 bedroom apartment in a prestigious Kilimani complex...', '../assets/images/properties/apartment1.jpg', 1),
(3, 'Prime Commercial Space in Westlands', 'commercial', 'rent', 250000, 0, 2, 300, 3, 2, 2018, 'Prime commercial space available in the heart of Westlands...', '../assets/images/properties/commercial1.jpg', 1),
(4, 'Elegant 5 Bedroom Mansion in Runda', 'residential', 'buy', 80000000, 5, 5, 500, 3, 5, 2017, 'Magnificent 5 bedroom mansion in the exclusive Runda estate...', '../assets/images/properties/mansion1.jpg', 1),
(5, 'Office Complex in Upper Hill', 'commercial', 'buy', 120000000, 0, 4, 1000, 20, 8, 2015, 'Prestigious office complex in Upper Hill...', '../assets/images/properties/office1.jpg', 1),
(6, 'Cozy 2 Bedroom Apartment in Lavington', 'residential', 'rent', 90000, 2, 2, 120, 1, 4, 2020, 'Cozy and modern 2 bedroom apartment in Lavington...', '../assets/images/properties/apartment2.jpg', 1);

-- Insert sample property images
INSERT IGNORE INTO property_images (property_id, image_path) VALUES
(1, '../assets/images/properties/villa1_1.jpg'),
(1, '../assets/images/properties/villa1_2.jpg'),
(1, '../assets/images/properties/villa1_3.jpg'),
(2, '../assets/images/properties/apartment1_1.jpg'),
(2, '../assets/images/properties/apartment1_2.jpg'),
(3, '../assets/images/properties/commercial1_1.jpg'),
(3, '../assets/images/properties/commercial1_2.jpg'),
(4, '../assets/images/properties/mansion1_1.jpg'),
(4, '../assets/images/properties/mansion1_2.jpg'),
(5, '../assets/images/properties/office1_1.jpg'),
(6, '../assets/images/properties/apartment2_1.jpg');

-- Insert sample property amenities
INSERT IGNORE INTO property_amenities (property_id, amenity_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11), (1, 12),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 6), (2, 8), (2, 13),
(3, 3), (3, 4), (3, 7), (3, 8), (3, 12), (3, 13),
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 7), (4, 8), (4, 9), (4, 10), (4, 11), (4, 12), (4, 15),
(5, 3), (5, 4), (5, 7), (5, 8), (5, 10), (5, 12), (5, 13),
(6, 1), (6, 2), (6, 3), (6, 4), (6, 6), (6, 8), (6, 9), (6, 13);