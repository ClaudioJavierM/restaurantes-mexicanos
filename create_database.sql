-- Database Setup Script for restaurantesmexicanos.com
-- Run this with MySQL root user:
-- mysql -u root -p < create_database.sql

-- Create database
CREATE DATABASE IF NOT EXISTS restaurantesmexicanos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user with strong password
-- IMPORTANT: Change 'StrongPassword123!' to a secure password
CREATE USER IF NOT EXISTS 'restaurantesmexicanos_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';

-- Grant privileges
GRANT ALL PRIVILEGES ON restaurantesmexicanos.* TO 'restaurantesmexicanos_user'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;

-- Show databases to confirm
SHOW DATABASES LIKE 'restaurantesmexicanos';

-- Show user to confirm
SELECT User, Host FROM mysql.user WHERE User = 'restaurantesmexicanos_user';
