-- Enhanced NBT Database Structure
-- Based on actual website code analysis
-- This creates all tables required by the current codebase with proper columns

USE nbt;

-- Drop simplified tables to recreate with proper structure
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

-- Create enhanced tables based on code analysis

-- 1. Admin table (enhanced with password hashing)
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) DEFAULT 'Admin',
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- 2. Courses table (enhanced structure from code)
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
    link VARCHAR(500) DEFAULT '',
    price DECIMAL(10,2) DEFAULT 0.00,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Team Members table (enhanced with image storage)
CREATE TABLE meet_our_team (
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
    image_data LONGBLOB,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Founder Cards table (for leadership section)
CREATE TABLE founder_card (
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
    image_data LONGBLOB,
    achievements TEXT DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Services table (enhanced with image storage)
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
    features TEXT DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Contact Us table (enhanced with status tracking)
CREATE TABLE contact_us (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    subject VARCHAR(255) DEFAULT '',
    message TEXT DEFAULT '',
    phone VARCHAR(20) DEFAULT '',
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    admin_notes TEXT DEFAULT '',
    ip_address VARCHAR(45) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. Testimonials table (enhanced)
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT '',
    company VARCHAR(255) DEFAULT '',
    position VARCHAR(255) DEFAULT '',
    message TEXT DEFAULT '',
    rating INT DEFAULT 5,
    image_name VARCHAR(255) DEFAULT '',
    image_data LONGBLOB,
    course_name VARCHAR(255) DEFAULT '',
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. Course Testimonials table (specialized for course reviews)
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

-- 9. Client Testimonials table (for business clients)
CREATE TABLE client_testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) DEFAULT '',
    company_email VARCHAR(255) DEFAULT '',
    linkedin VARCHAR(255) DEFAULT '',
    project_description TEXT DEFAULT '',
    rating INT DEFAULT 5,
    company_logo LONGBLOB,
    contact_person VARCHAR(255) DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10. Mission table (company information)
CREATE TABLE our_mission (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT 'Our Mission',
    description TEXT DEFAULT '',
    vision TEXT DEFAULT '',
    company_values TEXT DEFAULT '',
    students VARCHAR(100) DEFAULT '0+',
    courses VARCHAR(100) DEFAULT '0+',
    success_rate VARCHAR(100) DEFAULT '95%',
    years_experience VARCHAR(100) DEFAULT '5+',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 11. Social Media table (enhanced)
CREATE TABLE social_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(100) NOT NULL,
    platform_url VARCHAR(500) DEFAULT '',
    icon_class VARCHAR(100) DEFAULT '',
    followers INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 12. Client Management table (for business tracking)
CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) DEFAULT '',
    contact_email VARCHAR(255) DEFAULT '',
    phone VARCHAR(20) DEFAULT '',
    task TEXT DEFAULT '',
    duration VARCHAR(100) DEFAULT '',
    link VARCHAR(500) DEFAULT '',
    status ENUM('active', 'completed', 'pending', 'cancelled') DEFAULT 'active',
    notes TEXT DEFAULT '',
    budget VARCHAR(100) DEFAULT '',
    start_date DATE NULL,
    end_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 13. Coupons table (for discounts and promotions)
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT '',
    discount DECIMAL(5,2) DEFAULT 0.00,
    time_limit DATE NULL,
    usage_limit INT DEFAULT NULL,
    times_used INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 14. Overview Images table (for gallery/showcase)
CREATE TABLE overview_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT '',
    description TEXT DEFAULT '',
    image_sequence INT DEFAULT 0,
    image_name VARCHAR(255) DEFAULT '',
    image_type VARCHAR(100) DEFAULT '',
    image_size INT DEFAULT 0,
    image_data LONGBLOB,
    category VARCHAR(100) DEFAULT 'general',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user with hashed password
INSERT INTO admin (email, password, name) VALUES 
('admin@nbt.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'NBT Admin');

-- Insert default mission data
INSERT INTO our_mission (title, description, students, courses, success_rate, years_experience) VALUES 
('Empowering Future Innovators', 
 'At NBT, we are committed to providing world-class training and technology solutions that empower individuals and businesses to achieve their full potential in the digital age.',
 '500+', '25+', '95%', '8+');

SELECT 'Enhanced database structure created successfully!' as status;
SELECT 'Tables created:' as info;
SHOW TABLES;
