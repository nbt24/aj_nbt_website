-- Simple sample data insertion for NBT frontend testing
-- Inserts only essential data for main tables

USE nbt;

-- Clear existing sample data (keep admin user)
DELETE FROM training_courses;
DELETE FROM team_members;
DELETE FROM company_founders;
DELETE FROM business_services;
DELETE FROM social_media_links;
DELETE FROM contact_submissions;
DELETE FROM hero_carousel_images;
DELETE FROM student_reviews;
DELETE FROM company_testimonials;
DELETE FROM client_reviews;
DELETE FROM discount_codes;
DELETE FROM courses;
DELETE FROM meet_our_team;
DELETE FROM founder_card;
DELETE FROM our_services;
DELETE FROM social_media;
DELETE FROM contact_us;

-- Insert essential sample data

-- 1. Training Courses
INSERT INTO training_courses (title, description_1, description_2, educator, timeline, people, rating, link) VALUES 
('Full Stack Web Development', 'Complete web development course covering frontend and backend technologies', 'Learn React, Node.js, databases, and deployment with hands-on projects.', 'Sarah Johnson', '12 weeks', '25+ enrolled', 4.8, 'https://nbt.com/courses/fullstack');

-- 2. Team Members
INSERT INTO team_members (name, description, position, number, linkedin, email, image_name) VALUES 
('Sarah Johnson', 'Expert full-stack developer with 8+ years experience in building scalable web applications', 'Lead Developer', '+91-9876543210', 'https://linkedin.com/in/sarahjohnson', 'sarah@nbt.com', 'sarah-johnson.jpg');

-- 3. Company Founders
INSERT INTO company_founders (name, description, position, number, linkedin, email, image_name) VALUES 
('Rajesh Kumar', 'Visionary entrepreneur with 15+ years in technology and education, bridging the gap between academic learning and industry requirements.', 'CEO & Founder', '+91-9876543220', 'https://linkedin.com/in/rajeshkumar', 'rajesh@nbt.com', 'rajesh-kumar.jpg');

-- 4. Business Services
INSERT INTO business_services (title, description, points, price, image_name) VALUES 
('Custom Software Development', 'End-to-end software development services for businesses of all sizes', 'Scalable solutions, User-friendly interfaces, Security focused, 24/7 support', '₹50,000 - ₹5,00,000', 'software-dev.jpg');

-- 5. Social Media Links
INSERT INTO social_media_links (platform, platform_name, platform_url, icon_class, followers) VALUES 
('facebook', 'Facebook', 'https://facebook.com/nbttraining', 'fab fa-facebook-f', 5000);

-- 6. Contact Submissions
INSERT INTO contact_submissions (full_name, email_address, subject, message) VALUES 
('Priya Sharma', 'priya.sharma@email.com', 'Course Inquiry', 'Interested in Full Stack Web Development course. Please provide curriculum details.');

-- 7. Hero Carousel Images
INSERT INTO hero_carousel_images (title, image_sequence, image_name) VALUES 
('Transform Your Career with Technology', 1, 'hero-slide-1.jpg');

-- 8. Student Reviews
INSERT INTO student_reviews (name, email, course, rating, message, is_active) VALUES 
('Ankit Verma', 'ankit@email.com', 'Full Stack Web Development', 5, 'Excellent course with hands-on projects. Got placed in a top tech company!', 1);

-- 9. Company Testimonials
INSERT INTO company_testimonials (name, email, message, rating) VALUES 
('TechCorp Solutions', 'hr@techcorp.com', 'NBT has been our go-to partner for hiring skilled developers. Their students are well-prepared.', 5);

-- 10. Client Reviews
INSERT INTO client_reviews (company_name, company_email, linkedin, project_description, rating, is_active) VALUES 
('ShopEasy Ltd', 'contact@shopeasy.com', 'https://linkedin.com/company/shopeasy', 'E-commerce platform development with payment integration and analytics', 5, 1);

-- 11. Discount Codes
INSERT INTO discount_codes (code, discount_type, discount_value, description, valid_from, valid_until, usage_limit, is_active) VALUES 
('WELCOME2025', 'percentage', 20.00, 'New Year Special - 20% off on all courses', '2025-01-01', '2025-02-28', 100, 1);

-- 12. Courses (Local copy)
INSERT INTO courses (title, description_1, description_2, educator, timeline, people, rating, link) VALUES 
('Python Programming Masterclass', 'Comprehensive Python course from basics to advanced topics', 'Includes web frameworks, data science, automation with real-world projects.', 'Dr. Meera Patel', '10 weeks', '30+ enrolled', 4.7, 'https://nbt.com/courses/python');

-- 13. Meet Our Team (Local copy)
INSERT INTO meet_our_team (name, description, position, number, linkedin, email, image_name) VALUES 
('Rohit Sharma', 'Experienced data scientist with expertise in AI/ML technologies and business intelligence', 'Senior Data Scientist', '+91-9876543211', 'https://linkedin.com/in/rohitsharma', 'rohit@nbt.com', 'rohit-sharma.jpg');

-- 14. Founder Card (Local copy)
INSERT INTO founder_card (name, description, position, number, linkedin, email, image_name) VALUES 
('Neha Agarwal', 'Tech innovator and educator passionate about making technology accessible to everyone.', 'CTO & Co-Founder', '+91-9876543221', 'https://linkedin.com/in/nehaagarwal', 'neha@nbt.com', 'neha-agarwal.jpg');

-- 15. Our Services (Local copy)
INSERT INTO our_services (title, description, points, price, image_name) VALUES 
('Digital Marketing Solutions', 'Comprehensive digital marketing services including SEO, social media, and content creation', 'SEO Optimization, Social Media Management, Content Strategy, Analytics', '₹15,000 - ₹1,00,000', 'digital-marketing.jpg');

-- 16. Social Media (Local copy)
INSERT INTO social_media (platform, platform_name, platform_url, icon_class, followers) VALUES 
('instagram', 'Instagram', 'https://instagram.com/nbt_training', 'fab fa-instagram', 15000);

-- 17. Contact Us (Local copy)
INSERT INTO contact_us (full_name, email_address, subject, message) VALUES 
('Arjun Reddy', 'arjun.reddy@email.com', 'Corporate Training', 'Interested in corporate training programs for our 25-member development team.');

SELECT 'Sample data inserted successfully for frontend testing!' as status;
SELECT COUNT(*) as total_records_inserted FROM 
(SELECT id FROM training_courses UNION ALL
 SELECT id FROM team_members UNION ALL
 SELECT id FROM company_founders UNION ALL
 SELECT id FROM business_services UNION ALL
 SELECT id FROM social_media_links UNION ALL
 SELECT id FROM contact_submissions UNION ALL
 SELECT id FROM hero_carousel_images UNION ALL
 SELECT id FROM student_reviews UNION ALL
 SELECT id FROM company_testimonials UNION ALL
 SELECT id FROM client_reviews UNION ALL
 SELECT id FROM discount_codes UNION ALL
 SELECT id FROM courses UNION ALL
 SELECT id FROM meet_our_team UNION ALL
 SELECT id FROM founder_card UNION ALL
 SELECT id FROM our_services UNION ALL
 SELECT id FROM social_media UNION ALL
 SELECT id FROM contact_us) as all_records;
