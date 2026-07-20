-- =====================================================================
-- Food Ordering System - Database Schema
-- =====================================================================
-- Import this file in phpMyAdmin (or run via MySQL CLI / `mysql -u root -p < database.sql`)
-- It will create the database and all tables required by the PHP code.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS food_ordering
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE food_ordering;

-- ---------------------------------------------------------------------
-- Table: users
-- Used by: signup.php, login.php, forgot.php, reset_password.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: orders
-- Used by: orders.php, order_success.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dish_name VARCHAR(150) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  customer_name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  address TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: reviews
-- Used by: review.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  review TEXT NOT NULL,
  stars TINYINT NOT NULL DEFAULT 5,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: contact_messages
-- Used by: contact.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Sample data (optional - safe to delete if you don't want demo rows)
-- ---------------------------------------------------------------------
INSERT INTO reviews (name, review, stars) VALUES
('Kamal Perera', 'Amazing food and super fast delivery. Loved the biryani!', 5),
('Nimasha Silva', 'Good taste, but delivery was a bit late.', 4);
