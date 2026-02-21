-- Data Annotation Platform Database Schema
-- MySQL Database Schema for Laravel Backend

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `vip_tier` enum('free','bronze','silver','gold','platinum') NOT NULL DEFAULT 'free',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Profiles Table
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `total_earnings` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tasks_completed` int NOT NULL DEFAULT '0',
  `accuracy_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_profiles_user_id_unique` (`user_id`),
  CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wallets Table
CREATE TABLE IF NOT EXISTS `wallets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pending_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_earned` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_withdrawn` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wallets_user_id_unique` (`user_id`),
  CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Earnings Table
CREATE TABLE IF NOT EXISTS `earnings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('task_completion','bonus','referral','withdrawal') NOT NULL,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `earnings_user_id_index` (`user_id`),
  KEY `earnings_status_index` (`status`),
  CONSTRAINT `earnings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products Table
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `image_url` text NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `products_brand_index` (`brand`),
  KEY `products_category_index` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Brand Identification Tasks Table
CREATE TABLE IF NOT EXISTS `brand_identification_tasks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `status` enum('available','in_progress','completed','verified') NOT NULL DEFAULT 'available',
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `reward_amount` decimal(10,2) NOT NULL,
  `difficulty_level` enum('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tasks_product_id_index` (`product_id`),
  KEY `tasks_status_index` (`status`),
  KEY `tasks_assigned_to_index` (`assigned_to`),
  CONSTRAINT `tasks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Task Submissions Table
CREATE TABLE IF NOT EXISTS `task_submissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `selected_brand` varchar(255) NOT NULL,
  `confidence_level` enum('very_confident','confident','somewhat_confident','not_confident') NOT NULL,
  `notes` text,
  `is_correct` tinyint(1) DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `submissions_task_id_index` (`task_id`),
  KEY `submissions_user_id_index` (`user_id`),
  CONSTRAINT `submissions_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `brand_identification_tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment Methods Table
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `method_type` varchar(50) NOT NULL,
  `account_details` text NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payment_methods_user_id_index` (`user_id`),
  CONSTRAINT `payment_methods_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment Gateways Table
CREATE TABLE IF NOT EXISTS `payment_gateways` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `credentials` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `min_withdrawal` decimal(10,2) NOT NULL DEFAULT '10.00',
  `max_withdrawal` decimal(10,2) NOT NULL DEFAULT '10000.00',
  `processing_fee` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payment_gateways_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO `users` (`email`, `password`, `full_name`, `role`, `vip_tier`) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin', 'platinum')
ON DUPLICATE KEY UPDATE `email` = `email`;

-- Insert user profile for admin
INSERT INTO `user_profiles` (`user_id`, `total_earnings`, `tasks_completed`, `accuracy_score`)
SELECT `id`, 0.00, 0, 100.00 FROM `users` WHERE `email` = 'admin@example.com'
ON DUPLICATE KEY UPDATE `user_id` = `user_id`;

-- Insert wallet for admin
INSERT INTO `wallets` (`user_id`, `balance`, `pending_balance`, `total_earned`, `total_withdrawn`)
SELECT `id`, 0.00, 0.00, 0.00, 0.00 FROM `users` WHERE `email` = 'admin@example.com'
ON DUPLICATE KEY UPDATE `user_id` = `user_id`;

-- Sample Products
INSERT INTO `products` (`name`, `brand`, `category`, `image_url`, `description`) VALUES
('Premium Wireless Headphones', 'Sony', 'Electronics', '/products/P1.jpg', 'High-quality wireless headphones with noise cancellation'),
('Smart Watch Pro', 'Apple', 'Electronics', '/products/P2.jpg', 'Advanced fitness tracking and notifications'),
('Running Shoes Elite', 'Nike', 'Sports', '/products/P3.jpg', 'Professional running shoes for athletes'),
('Laptop Ultrabook', 'Dell', 'Computers', '/products/P4.jpg', 'Lightweight and powerful laptop'),
('Gaming Console', 'Sony', 'Gaming', '/products/P5.jpg', 'Next-gen gaming experience')
ON DUPLICATE KEY UPDATE `name` = `name`;

COMMIT;
