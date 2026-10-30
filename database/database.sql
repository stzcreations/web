-- Project Download Library Database
-- Pure PHP + MySQL

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS `stzcreations`;
CREATE DATABASE `stzcreations` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `stzcreations`;

-- admins
CREATE TABLE `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- categories
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `description` TEXT,
  `status` ENUM('active','inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- projects
CREATE TABLE `projects` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `short_description` VARCHAR(500) DEFAULT NULL,
  `description` LONGTEXT,
  `features` LONGTEXT,
  `version` VARCHAR(50) DEFAULT '1.0',
  `file_type` VARCHAR(20) DEFAULT 'ZIP',
  `file_size` VARCHAR(50) DEFAULT NULL,
  `file_path` VARCHAR(500) DEFAULT NULL,
  `thumbnail` VARCHAR(500) DEFAULT NULL,
  `seo_title` VARCHAR(200) DEFAULT NULL,
  `meta_description` VARCHAR(300) DEFAULT NULL,
  `status` ENUM('published','draft') DEFAULT 'draft',
  `featured` TINYINT(1) DEFAULT 0,
  `download_count` INT UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_featured` (`featured`),
  INDEX `idx_created` (`created_at`),
  INDEX `idx_downloads` (`download_count`),
  INDEX `idx_updated` (`updated_at`),
  INDEX `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- project_screenshots
CREATE TABLE `project_screenshots` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(500) NOT NULL,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  INDEX `idx_project` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- downloads
CREATE TABLE `downloads` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `downloaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  INDEX `idx_project` (`project_id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_date` (`downloaded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- advertisements
CREATE TABLE `advertisements` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `image_path` VARCHAR(500) DEFAULT NULL,
  `url` VARCHAR(500) NOT NULL,
  `code` LONGTEXT DEFAULT NULL,
  `position` ENUM('header','homepage','sidebar','footer','socialbar') DEFAULT 'homepage',
  `status` ENUM('enabled','disabled') DEFAULT 'enabled',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_position` (`position`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- website_settings
CREATE TABLE `website_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` LONGTEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- visitors
CREATE TABLE `visitors` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `page` VARCHAR(255) DEFAULT NULL,
  `visited_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_date` (`visited_at`),
  INDEX `idx_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (password: admin123)
INSERT INTO `admins` (`username`,`email`,`password`,`name`) VALUES
('admin','admin@example.com','$2y$10$LqWTGjWWUSbf4SlAj2ZmYO.zftytY8Z/ijOBefhItnVUxBKDUdwCi','Administrator');

-- Categories
INSERT INTO `categories` (`name`,`slug`,`description`,`status`) VALUES
('PHP Projects','php-projects','Pure PHP web projects','active'),
('Laravel Projects','laravel-projects','Laravel framework projects','active'),
('HTML Templates','html-templates','HTML/CSS templates','active'),
('CSS Templates','css-templates','CSS templates','active'),
('JavaScript Projects','javascript-projects','JavaScript projects','active'),
('Website Templates','website-templates','Complete website templates','active'),
('WordPress Themes','wordpress-themes','WordPress themes','active'),
('E-commerce Projects','ecommerce-projects','E-commerce projects','active'),
('Admin Dashboards','admin-dashboards','Admin dashboard templates','active'),
('Other Projects','other-projects','Other miscellaneous projects','active');

-- Website settings
INSERT INTO `website_settings` (`setting_key`,`setting_value`) VALUES
('site_name','STZ Creations'),
('site_description','Ready-to-use web projects, free to download'),
('site_logo',''),
('site_favicon',''),
('contact_email','admin@example.com'),
('facebook_url',''),
('twitter_url',''),
('instagram_url',''),
('github_url',''),
('footer_text','STZ Creations - Free web projects'),
('copyright_text','© 2026 STZ Creations. All rights reserved.'),
('meta_image',''),
('seo_title','STZ Creations'),
('seo_description','Download ready-to-use web projects for free'),
('download_countdown','60'),
('download_ad_url','https://www.profitableratecpmnetwork.com/h7mm3034rf?key=71a92769225b798cc017c15982d72277'),
('footer_scripts','<script data-cfasync="false" src="https://bauval.org/14/1662e76344132687b6fe1e50d086e85a"></script>'),
('google_client_id',''),
('google_client_secret',''),
('google_redirect_uri',''),
('default_ad_slot','101f9cf4aa9700db314ed8908a39fd03');

-- Default advertisement
INSERT INTO `advertisements` (`title`,`image_path`,`url`,`position`,`status`) VALUES
('Sponsored Ad','','https://www.profitableratecpmnetwork.com/h7mm3034rf?key=71a92769225b798cc017c15982d72277','header','enabled'),
('Sponsored Ad','','https://www.profitableratecpmnetwork.com/h7mm3034rf?key=71a92769225b798cc017c15982d72277','homepage','enabled'),
('Sponsored Ad','','https://www.profitableratecpmnetwork.com/h7mm3034rf?key=71a92769225b798cc017c15982d72277','footer','enabled');

-- users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `google_id` VARCHAR(255) NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `profile_picture` VARCHAR(500) NULL,
  `email_verified` TINYINT(1) DEFAULT 0,
  `status` ENUM('active','inactive','suspended') DEFAULT 'active',
  `password` VARCHAR(255) NULL,
  `last_login_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
