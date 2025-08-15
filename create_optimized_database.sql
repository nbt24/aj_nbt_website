-- Optimized NBT Database Structure
-- Based on frontend code analysis and admin panel requirements
-- Focused on most relevant fields for user experience

USE nbt;

-- Drop all existing tables to recreate with optimized structure
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS meet_our_team;
DROP TABLE IF EXISTS founder_card;
DROP TABLE IF EXISTS our_services;
DROP TABLE IF EXISTS contact_us;
DROP TABLE IF EXISTS testimonials;
DROP TABLE IF EXISTS course_testimonials;
DROP TABLE IF EXISTS client_testimonials;
DROP TABLE IF EXISTS our_mission;
DROP TABLE IF EXISTS social_media;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS overview_images;

-- 1. ADMIN TABLE (Simplified for easy management)
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. COURSES TABLE (Optimized for frontend display)
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image LONGBLOB,
    type VARCHAR(100) DEFAULT 'Online',
    description_1 VARCHAR(500) DEFAULT '',
    description_2 TEXT DEFAULT '',
    educator VARCHAR(255) DEFAULT '',
    timeline VARCHAR(100) DEFAULT '',
    people VARCHAR(100) DEFAULT '',
    rating DECIMAL(3,1) DEFAULT 5.0,
    price DECIMAL(10,2) DEFAULT 0.00,
    link VARCHAR(500) DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. TEAM MEMBERS TABLE (Focused on essential display fields)
CREATE TABLE meet_our_team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255) DEFAULT '',
    description TEXT DEFAULT '',
    image_sequence INT DEFAULT 0,
    linkedin VARCHAR(500) DEFAULT '',
    email VARCHAR(255) DEFAULT '',
    phone VARCHAR(20) DEFAULT '',
    image_name VARCHAR(255) DEFAULT '',
    image_type VARCHAR(100) DEFAULT '',
    image_size INT DEFAULT 0,
    image_data LONGBLOB,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. FOUNDER CARDS TABLE (Leadership showcase)
CREATE TABLE founder_card (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255) DEFAULT '',
    description TEXT DEFAULT '',
    image_sequence INT DEFAULT 0,
    linkedin VARCHAR(500) DEFAULT '',
    email VARCHAR(255) DEFAULT '',
    achievements TEXT DEFAULT '',
    image_name VARCHAR(255) DEFAULT '',
    image_type VARCHAR(100) DEFAULT '',
    image_size INT DEFAULT 0,
    image_data LONGBLOB,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. SERVICES TABLE (Key business offerings)
CREATE TABLE our_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT '',
    points TEXT DEFAULT '',
    price VARCHAR(100) DEFAULT '',
    image_name VARCHAR(255) DEFAULT '',
    image_type VARCHAR(100) DEFAULT '',
    image_size INT DEFAULT 0,
    image_data LONGBLOB,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. CONTACT SUBMISSIONS TABLE (Customer inquiries)
CREATE TABLE contact_us (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    subject VARCHAR(255) DEFAULT '',
    message TEXT DEFAULT '',
    phone VARCHAR(20) DEFAULT '',
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    admin_notes TEXT DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. GENERAL TESTIMONIALS TABLE (Customer feedback)
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT '',
    message TEXT DEFAULT '',
    rating INT DEFAULT 5,
    course_name VARCHAR(255) DEFAULT '',
    company VARCHAR(255) DEFAULT '',
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. COURSE TESTIMONIALS TABLE (Course-specific reviews)
CREATE TABLE course_testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT '',
    course VARCHAR(255) DEFAULT '',
    rating INT DEFAULT 5,
    message TEXT DEFAULT '',
    image LONGBLOB,
    video LONGBLOB,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 9. CLIENT TESTIMONIALS TABLE (Business client reviews)
CREATE TABLE client_testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) DEFAULT '',
    company_email VARCHAR(255) DEFAULT '',
    linkedin VARCHAR(255) DEFAULT '',
    project_description TEXT DEFAULT '',
    rating INT DEFAULT 5,
    company_logo LONGBLOB,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10. MISSION TABLE (Company information)
CREATE TABLE our_mission (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT 'Our Mission',
    description TEXT DEFAULT '',
    students VARCHAR(100) DEFAULT '0+',
    courses VARCHAR(100) DEFAULT '0+',
    success_rate VARCHAR(100) DEFAULT '95%',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 11. SOCIAL MEDIA TABLE (Platform links)
CREATE TABLE social_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(100) NOT NULL,
    followers INT DEFAULT 0,
    platform_url VARCHAR(500) DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 12. CLIENT MANAGEMENT TABLE (Project tracking)
CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) DEFAULT '',
    contact_email VARCHAR(255) DEFAULT '',
    task TEXT DEFAULT '',
    duration VARCHAR(100) DEFAULT '',
    link VARCHAR(500) DEFAULT '',
    status ENUM('active', 'completed', 'pending', 'cancelled') DEFAULT 'active',
    notes TEXT DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 13. COUPONS TABLE (Discount system)
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT '',
    discount DECIMAL(5,2) DEFAULT 0.00,
    time_limit DATE NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 14. OVERVIEW IMAGES TABLE (Gallery showcase)
CREATE TABLE overview_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT '',
    image_sequence INT DEFAULT 0,
    image_name VARCHAR(255) DEFAULT '',
    image_type VARCHAR(100) DEFAULT '',
    image_size INT DEFAULT 0,
    image_data LONGBLOB,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert optimized sample data
INSERT INTO admin (email, password, name) VALUES 
('admin@nbt.com', 'admin123', 'NBT Admin');

INSERT INTO our_mission (title, description, students, courses, success_rate) VALUES 
('Empowering Tech Careers', 
 'We provide industry-focused training programs that transform careers and build the next generation of tech professionals.',
 '1000+', '15+', '95%');

SELECT 'Optimized database structure created successfully!' as status;
SHOW TABLES;
