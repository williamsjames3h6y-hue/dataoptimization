-- Database Schema for Earnings Platform

CREATE DATABASE IF NOT EXISTS earnings_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE earnings_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    balance DECIMAL(10, 2) DEFAULT 0.00,
    total_earned DECIMAL(10, 2) DEFAULT 0.00,
    tasks_completed INT DEFAULT 0,
    vip_tier VARCHAR(50) DEFAULT 'Free',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table (for annotation tasks)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    brand VARCHAR(255),
    image_url TEXT,
    category VARCHAR(100),
    reward DECIMAL(10, 2) DEFAULT 0.50,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tasks table (user annotation submissions)
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    brand_selection VARCHAR(255),
    confidence_level INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reward_amount DECIMAL(10, 2) DEFAULT 0.00,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- VIP Tiers table
CREATE TABLE IF NOT EXISTS vip_tiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) DEFAULT 0.00,
    daily_tasks INT DEFAULT 10,
    task_reward DECIMAL(10, 2) DEFAULT 0.50,
    features JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('earning', 'withdrawal', 'upgrade') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO users (email, password, full_name, role) VALUES
('admin@earnings.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin');

-- Insert sample VIP tiers
INSERT INTO vip_tiers (name, description, price, daily_tasks, task_reward, features) VALUES
('Free', 'Basic tier for new users', 0.00, 10, 0.50, '["Access to basic tasks", "Standard support"]'),
('Bronze', 'Entry level VIP membership', 9.99, 20, 0.75, '["Increased daily tasks", "Higher rewards", "Priority support"]'),
('Silver', 'Mid-tier VIP membership', 19.99, 35, 1.00, '["More daily tasks", "Better rewards", "Priority support", "Exclusive tasks"]'),
('Gold', 'Premium VIP membership', 49.99, 50, 1.50, '["Maximum daily tasks", "Premium rewards", "24/7 support", "Exclusive tasks", "Bonus opportunities"]');

-- Insert sample products
INSERT INTO products (name, brand, image_url, category, reward) VALUES
('Wireless Headphones', 'TechBrand', '/products/P1.jpg', 'Electronics', 0.50),
('Smart Watch', 'TechBrand', '/products/P2.jpg', 'Electronics', 0.50),
('Running Shoes', 'SportsBrand', '/products/P3.jpg', 'Sports', 0.50),
('Backpack', 'TravelCo', '/products/P4.jpg', 'Travel', 0.50),
('Water Bottle', 'EcoBrand', '/products/P5.jpg', 'Lifestyle', 0.50);
