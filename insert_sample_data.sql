-- Insert sample data into all NBT database tables
-- This script inserts exactly 1 row into each table for frontend testing

USE nbt;

-- Insert data with correct column names based on actual table structures

-- 1. administrators (already has admin@nbt.com)
INSERT IGNORE INTO administrators (email, password) VALUES 
('john.doe@nbt.com', 'password123');

-- 2. training_courses
INSERT INTO training_courses (title, description_1, description_2, educator, timeline, people, rating, link) VALUES 
('Full Stack Web Development', 'Complete web development course covering frontend and backend technologies', 'Covers React, Node.js, databases, and deployment. Includes hands-on projects and placement assistance.', 'Sarah Johnson', '12 weeks', '25+ enrolled', 4.8, 'https://nbt.com/courses/fullstack');

-- 3. team_members
INSERT INTO team_members (name, description, position, number, linkedin, email, image_name) VALUES 
('Sarah Johnson', 'Expert in full-stack development with 8+ years of experience in building scalable web applications', 'Lead Developer', '+91-9876543210', 'https://linkedin.com/in/sarahjohnson', 'sarah@nbt.com', 'sarah-johnson.jpg');

-- 4. company_founders
INSERT INTO company_founders (name, description, position, number, linkedin, email, image_name) VALUES 
('Rajesh Kumar', 'Visionary entrepreneur with 15+ years of experience in technology and education. Founded NBT to bridge the gap between academic learning and industry requirements.', 'CEO & Founder', '+91-9876543220', 'https://linkedin.com/in/rajeshkumar', 'rajesh@nbt.com', 'rajesh-kumar.jpg');

-- 5. business_services
INSERT INTO business_services (title, description, points, price, image_name) VALUES 
('Custom Software Development', 'End-to-end software development services for businesses of all sizes.', 'Scalable solutions, User-friendly interfaces, Security focused, 24/7 support', '₹50,000 - ₹5,00,000', 'software-dev.jpg');

-- 6. social_media_links
INSERT INTO social_media_links (platform, platform_name, platform_url, icon_class, followers) VALUES 
('facebook', 'Facebook', 'https://facebook.com/nbttraining', 'fab fa-facebook-f', 5000);

-- 7. contact_submissions
INSERT INTO contact_submissions (full_name, email_address, subject, message) VALUES 
('Priya Sharma', 'priya.sharma@email.com', 'Course Inquiry', 'I am interested in your Full Stack Web Development course. Could you please provide more details about the curriculum and placement assistance?');

-- 8. company_mission
INSERT INTO company_mission (title, description, students, courses, success_rate) VALUES 
('Empowering Future Innovators', 'At NBT, we are committed to providing world-class training and technology solutions that empower individuals and businesses to achieve their full potential in the digital age.', '500+', '25+', '95%');

-- 9. hero_carousel_images
INSERT INTO hero_carousel_images (title, image_sequence, image_name) VALUES 
('Transform Your Career with Technology', 1, 'hero-slide-1.jpg');

-- 10. student_reviews
INSERT INTO student_reviews (name, email, course, rating, message, is_active) VALUES 
('Ankit Verma', 'ankit@email.com', 'Full Stack Web Development', 5, 'Excellent course with hands-on projects. The instructors are very knowledgeable and supportive. I got placed in a top tech company within 2 months of completion.', 1);

-- 11. company_testimonials
INSERT INTO company_testimonials (company_name, description) VALUES 
('TechCorp Solutions', 'NBT has been our go-to partner for hiring skilled developers. Their students are well-prepared and demonstrate excellent technical and problem-solving skills.');

-- 12. client_projects
INSERT INTO client_projects (company_name, description) VALUES 
('ShopEasy Ltd', 'Complete e-commerce solution with payment gateway integration, inventory management, and customer analytics dashboard');

-- 13. client_reviews
INSERT INTO client_reviews (client_name, company_name, rating, review_text, project_name) VALUES 
('Vikash Singh', 'ShopEasy Ltd', 5, 'Outstanding work on our e-commerce platform. The team delivered on time and exceeded our expectations. Highly recommended for any web development project.', 'E-Commerce Platform');

-- 14. discount_codes
INSERT INTO discount_codes (code, description, discount_percentage, valid_from, valid_until, is_active) VALUES 
('WELCOME2025', 'New Year Special - Get 20% off on all courses', 20.00, '2025-01-01', '2025-02-28', 1);

-- 15. courses (same structure as training_courses)
INSERT INTO courses (title, description_1, description_2, educator, timeline, people, rating, link) VALUES 
('Python Programming Masterclass', 'Comprehensive Python course covering basics to advanced topics', 'Includes web frameworks, data science, automation, and real-world projects with placement support.', 'Dr. Meera Patel', '10 weeks', '30+ enrolled', 4.7, 'https://nbt.com/courses/python');

-- 16. meet_our_team (same structure as team_members)
INSERT INTO meet_our_team (name, description, position, number, linkedin, email, image_name) VALUES 
('Rohit Sharma', 'Experienced data scientist and machine learning engineer with expertise in AI/ML technologies and business intelligence', 'Senior Data Scientist', '+91-9876543211', 'https://linkedin.com/in/rohitsharma', 'rohit@nbt.com', 'rohit-sharma.jpg');

-- 17. founder_card (same structure as company_founders)
INSERT INTO founder_card (name, description, position, number, linkedin, email, image_name) VALUES 
('Neha Agarwal', 'Tech innovator and educator with a passion for making technology accessible to everyone. Co-founded NBT with a vision to democratize quality tech education.', 'CTO & Co-Founder', '+91-9876543221', 'https://linkedin.com/in/nehaagarwal', 'neha@nbt.com', 'neha-agarwal.jpg');

-- 18. our_services (same structure as business_services)
INSERT INTO our_services (title, description, points, price, image_name) VALUES 
('Digital Marketing Solutions', 'Comprehensive digital marketing services including SEO, social media marketing, content creation, and analytics', 'SEO Optimization, Social Media Management, Content Strategy, Analytics Dashboard, PPC Campaigns', '₹15,000 - ₹1,00,000', 'digital-marketing.jpg');

-- 19. social_media (same structure as social_media_links)
INSERT INTO social_media (platform, platform_name, platform_url, icon_class, followers) VALUES 
('instagram', 'Instagram', 'https://instagram.com/nbt_training', 'fab fa-instagram', 15000);

-- 20. contact_us (same structure as contact_submissions)
INSERT INTO contact_us (full_name, email_address, subject, message) VALUES 
('Arjun Reddy', 'arjun.reddy@email.com', 'Corporate Training Inquiry', 'I would like to know more about your corporate training programs for our software development team of 25 members.');

-- 21. overview_images
INSERT INTO overview_images (title, image_path, description, category, display_order) VALUES 
('Modern Learning Environment', 'uploads/classroom-overview.jpg', 'State-of-the-art classrooms equipped with latest technology for immersive learning experience', 'facilities', 1);

-- 22. testimonials
INSERT INTO testimonials (student_name, course_name, rating, review_text, image_name, graduation_date) VALUES 
('Kavita Joshi', 'Digital Marketing Masterclass', 5, 'The digital marketing course helped me transition from a traditional marketing role to digital. The practical approach and real-world projects made all the difference.', 'kavita-joshi.jpg', '2024-11-30');

-- 23. course_testimonials
INSERT INTO course_testimonials (student_name, course_name, rating, review_text, image_name, batch_year) VALUES 
('Suresh Kumar', 'Python Programming Masterclass', 4, 'Great course structure with excellent hands-on practice. The instructor explained complex concepts in a very simple manner. Highly recommended for beginners.', 'suresh-kumar.jpg', '2024');

-- 24. client_testimonials
INSERT INTO client_testimonials (client_name, company_name, rating, review_text, project_name, completion_year) VALUES 
('Ravi Patel', 'InnovateTech Pvt Ltd', 5, 'NBT delivered an exceptional mobile app that exceeded our expectations. Their attention to detail and technical expertise is commendable.', 'Mobile Banking App', '2024');

-- 25. coupons
INSERT INTO coupons (code, description, discount_percentage, valid_from, valid_until, is_active, usage_limit) VALUES 
('STUDENT50', 'Special discount for students - 50% off on selected courses', 50.00, '2025-01-01', '2025-12-31', 1, 100);

-- 26. client
INSERT INTO client (company_name, contact_person, email, phone, project_description, status, budget_range) VALUES 
('FinanceFlow Solutions', 'Deepak Malhotra', 'deepak@financeflow.com', '+91-9876543203', 'Need a comprehensive financial management system with reporting and analytics capabilities', 'active', '₹2,00,000 - ₹5,00,000');

-- Show summary of inserted data
SELECT 'Data insertion completed successfully!' as status;
