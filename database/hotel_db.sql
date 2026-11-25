-- ============================================
-- Grand Horizon Hotel Management System
-- Import this file in phpMyAdmin (or: mysql -u root < hotel_db.sql)
-- ============================================
CREATE DATABASE IF NOT EXISTS hotel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE hotel_db;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  phone VARCHAR(20) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('guest','admin') NOT NULL DEFAULT 'guest',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_number VARCHAR(10) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  type VARCHAR(40) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  capacity INT NOT NULL DEFAULT 2,
  amenities VARCHAR(255) DEFAULT NULL,
  description TEXT,
  image VARCHAR(255) DEFAULT 'standard.jpg',
  status ENUM('available','maintenance') NOT NULL DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  room_id INT NOT NULL,
  check_in DATE NOT NULL,
  check_out DATE NOT NULL,
  guests INT NOT NULL DEFAULT 1,
  total_price DECIMAL(10,2) NOT NULL,
  special_request VARCHAR(255) DEFAULT NULL,
  status ENUM('pending','confirmed','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL,
  subject VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Demo accounts: admin@hotel.com / admin123   |   guest@hotel.com / guest123
INSERT INTO users (name,email,phone,password,role) VALUES
('Hotel Admin','admin@hotel.com','9000000000','$2y$10$abcdefghijklmnopqrstuuEZ4tpXm4W5SeJkDbuNdfJ.CF0.Wc2V2','admin'),
('Demo Guest','guest@hotel.com','9111111111','$2y$10$zyxwvutsrqponmlkjihgfeyM5exzX3aqKemmhqiYB29s/KPmVbZGm','guest');

INSERT INTO rooms (room_number,name,type,price,capacity,amenities,description,image) VALUES
('101','Cozy Single Room','Single',1800.00,1,'Free WiFi,AC,TV,Work Desk','A compact and comfortable room ideal for solo travellers and business guests, with a work desk and city view.','single.jpg'),
('102','Standard Double Room','Standard',2800.00,2,'Free WiFi,AC,TV,Mini Fridge','A warm double room with a queen bed, tea/coffee maker and fast WiFi for a relaxing stay.','standard.jpg'),
('201','Deluxe King Room','Deluxe',4200.00,2,'Free WiFi,AC,Smart TV,Mini Bar,Bathtub','Spacious deluxe room with a king bed, smart TV, mini bar and a luxury bathtub.','deluxe.jpg'),
('202','Family Room','Family',5200.00,4,'Free WiFi,AC,TV,Two Beds,Breakfast','Perfect for families: extra space, two beds, complimentary breakfast and a kids corner.','family.jpg'),
('301','Executive Suite','Suite',7500.00,3,'Free WiFi,AC,Living Area,Mini Bar,Balcony','An elegant suite with a separate living area, private balcony and panoramic views.','suite.jpg'),
('401','Presidential Suite','Presidential',14000.00,4,'Free WiFi,AC,Jacuzzi,Butler Service,Private Lounge','Our finest suite with a jacuzzi, private lounge, 24/7 butler service and premium interiors.','presidential.jpg');

INSERT INTO bookings (user_id,room_id,check_in,check_out,guests,total_price,status) VALUES
(2,3,DATE_ADD(CURDATE(),INTERVAL 5 DAY),DATE_ADD(CURDATE(),INTERVAL 8 DAY),2,12600.00,'confirmed'),
(2,1,DATE_SUB(CURDATE(),INTERVAL 20 DAY),DATE_SUB(CURDATE(),INTERVAL 18 DAY),1,3600.00,'checked_out');
