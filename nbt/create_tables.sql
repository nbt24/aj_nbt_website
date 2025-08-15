-- NBT Database Creation Script
-- Copy and paste this into phpMyAdmin SQL tab if you need to create the database manually

-- Create database (run this first if database doesn't exist)
CREATE DATABASE IF NOT EXISTS nbt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nbt;

-- 1. Admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Our mission table
CREATE TABLE IF NOT EXISTS our_mission (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT 'Our Mission',
    description TEXT DEFAULT 'Empowering careers and businesses through cutting-edge courses, expert consultancy, and B2B tech solutions tailored for real-world impact.',
    students VARCHAR(100) DEFAULT '500',
    courses VARCHAR(100) DEFAULT '25',
    success_rate VARCHAR(100) DEFAULT '95',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image LONGBLOB NULL,
    type VARCHAR(100) DEFAULT 'Online',
    description_1 VARCHAR(500) DEFAULT '',
    description_2 TEXT DEFAULT '',
    educator VARCHAR(255) DEFAULT '',
    timeline VARCHAR(100) DEFAULT '',
    people VARCHAR(100) DEFAULT '',
    rating DECIMAL(3,1) DEFAULT 5.0,
    link VARCHAR(500) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Meet our team table
CREATE TABLE IF NOT EXISTS meet_our_team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT '',
    position VARCHAR(255) DEFAULT '',
    number VARCHAR(50) DEFAULT '',
    image_sequence INT DEFAULT 0,
    linkedin VARCHAR(500) DEFAULT '',
    email VARCHAR(255) DEFAULT '',
    image_name VARCHAR(255) DEFAULT '',
    image_type VARCHAR(100) DEFAULT '',
    image_size INT DEFAULT 0,
    image_data LONGBLOB NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Founder card table
CREATE TABLE IF NOT EXISTS founder_card (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT '',
    position VARCHAR(255) DEFAULT '',
    number VARCHAR(50) DEFAULT '',
    image_sequence INT DEFAULT 0,
    linkedin VARCHAR(500) DEFAULT '',
    email VARCHAR(255) DEFAULT '',
    image_name VARCHAR(255) DEFAULT '',
    image_type VARCHAR(100) DEFAULT '',
    image_size INT DEFAULT 0,
    image_data LONGBLOB NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Our services table
CREATE TABLE IF NOT EXISTS our_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT '',
    points TEXT DEFAULT '',
    price VARCHAR(100) DEFAULT '',
    image_name VARCHAR(255) DEFAULT '',
    image_type VARCHAR(100) DEFAULT '',
    image_size INT DEFAULT 0,
    image_data LONGBLOB NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. Testimonials table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT '',
    message TEXT DEFAULT '',
    rating INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. Overview images table
CREATE TABLE IF NOT EXISTS overview_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT '',
    image_sequence INT DEFAULT 0,
    image_name VARCHAR(255) DEFAULT '',
    image_type VARCHAR(100) DEFAULT '',
    image_size INT DEFAULT 0,
    image_data LONGBLOB NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 9. Client table
CREATE TABLE IF NOT EXISTS client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) DEFAULT '',
    company_name VARCHAR(255) DEFAULT '',
    task TEXT DEFAULT '',
    duration VARCHAR(100) DEFAULT '',
    link VARCHAR(500) DEFAULT '',
    status VARCHAR(100) DEFAULT 'Active',
    contact_email VARCHAR(255) DEFAULT '',
    notes TEXT DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10. Coupons table
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) UNIQUE NOT NULL,
    discount INT DEFAULT 0,
    time_limit VARCHAR(255) DEFAULT '',
    category VARCHAR(100) DEFAULT 'General',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 11. Client testimonials table
CREATE TABLE IF NOT EXISTS client_testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) DEFAULT '',
    company_email VARCHAR(255) DEFAULT '',
    linkedin VARCHAR(500) DEFAULT '',
    project_description TEXT DEFAULT '',
    rating INT DEFAULT 5,
    company_logo LONGBLOB NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 12. Course testimonials table
CREATE TABLE IF NOT EXISTS course_testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) DEFAULT '',
    email VARCHAR(255) DEFAULT '',
    course VARCHAR(255) DEFAULT '',
    rating INT DEFAULT 5,
    message TEXT DEFAULT '',
    image LONGBLOB NULL,
    video LONGBLOB NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 13. Social media table
CREATE TABLE IF NOT EXISTS social_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(100) NOT NULL,
    followers INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 14. Contact us table
CREATE TABLE IF NOT EXISTS contact_us (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    subject VARCHAR(255) DEFAULT '',
    message TEXT DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default data
INSERT IGNORE INTO admin (email, password) VALUES ('admin@nbt.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT IGNORE INTO our_mission (id, title, description, students, courses, success_rate) VALUES 
(1, 'Our Mission', 'Empowering careers and businesses through cutting-edge courses, expert consultancy, and B2B tech solutions tailored for real-world impact.', '500', '25', '95');

INSERT IGNORE INTO social_media (platform, followers) VALUES 
('LinkedIn', 1200),
('Instagram', 850),
('YouTube', 2500),
('Twitter', 650);

INSERT IGNORE INTO courses (id, title, type, description_1, description_2, educator, timeline, people, rating, link) VALUES 
(1, 'Web Development Bootcamp', 'Online', 'Full Stack', 'Learn HTML, CSS, JavaScript, React, Node.js', 'John Doe', '12 weeks', '25', 4.8, 'https://courses.nextbiggtech.com');

INSERT IGNORE INTO testimonials (id, name, email, message, rating) VALUES 
(1, 'Sarah Johnson', 'sarah@example.com', 'Amazing courses! Really helped me advance my career.', 5);
