-- Insert comprehensive sample data for all enhanced tables
-- This populates all tables with realistic NBT business data

USE nbt;

-- Clear any existing data
DELETE FROM courses;
DELETE FROM meet_our_team;
DELETE FROM founder_card;
DELETE FROM our_services;
DELETE FROM contact_us;
DELETE FROM testimonials;
DELETE FROM course_testimonials;
DELETE FROM client_testimonials;
DELETE FROM social_media;
DELETE FROM client;
DELETE FROM coupons;
DELETE FROM overview_images;

-- 1. Courses (enhanced with all fields)
INSERT INTO courses (title, type, description_1, description_2, educator, timeline, people, rating, price, is_active) VALUES 
('Full Stack Web Development Bootcamp', 'Online', 'Master modern web development with React, Node.js, and MongoDB', 'Complete hands-on training covering frontend, backend, databases, and deployment. Includes 5 real-world projects, placement assistance, and lifetime support.', 'Sarah Johnson', '16 weeks', '150+ enrolled', 4.8, 45000.00, 1),
('Python Programming & Data Science', 'Hybrid', 'Learn Python, data analysis, machine learning, and AI fundamentals', 'Comprehensive course covering Python basics to advanced topics including pandas, numpy, scikit-learn, and deep learning with TensorFlow.', 'Dr. Rajesh Kumar', '12 weeks', '200+ enrolled', 4.9, 35000.00, 1),
('Digital Marketing Mastery', 'Online', 'Complete digital marketing course with SEO, SEM, and social media', 'Master Google Ads, Facebook Ads, content marketing, email marketing, and analytics. Includes Google and Facebook certifications.', 'Priya Sharma', '8 weeks', '300+ enrolled', 4.7, 25000.00, 1);

-- 2. Team Members
INSERT INTO meet_our_team (name, description, position, number, image_sequence, linkedin, email, image_name, is_active) VALUES 
('Sarah Johnson', 'Full-stack developer with 8+ years experience at top tech companies. Expert in React, Node.js, and cloud technologies.', 'Lead Technical Instructor', '+91-9876543210', 1, 'https://linkedin.com/in/sarahjohnson', 'sarah@nbt.com', 'sarah-johnson.jpg', 1),
('Dr. Rajesh Kumar', 'PhD in Computer Science with 12+ years in data science and AI research. Former senior data scientist at Microsoft.', 'Head of Data Science', '+91-9876543211', 2, 'https://linkedin.com/in/rajeshkumar', 'rajesh@nbt.com', 'rajesh-kumar.jpg', 1),
('Priya Sharma', 'Digital marketing expert with 6+ years helping brands grow online. Google and Facebook certified marketing professional.', 'Marketing Head', '+91-9876543212', 3, 'https://linkedin.com/in/priyasharma', 'priya@nbt.com', 'priya-sharma.jpg', 1);

-- 3. Founder Cards
INSERT INTO founder_card (name, description, position, number, image_sequence, linkedin, email, image_name, achievements, is_active) VALUES 
('Amit Agarwal', 'Visionary entrepreneur with 15+ years in technology and education. Founded NBT to bridge the gap between industry and academia.', 'CEO & Founder', '+91-9876543200', 1, 'https://linkedin.com/in/amitag', 'amit@nbt.com', 'amit-agarwal.jpg', 'Founded 3 successful startups, Mentored 5000+ students, TEDx Speaker', 1),
('Neha Singh', 'Technology leader and educator passionate about making quality tech education accessible to everyone.', 'CTO & Co-Founder', '+91-9876543201', 2, 'https://linkedin.com/in/nehasingh', 'neha@nbt.com', 'neha-singh.jpg', 'Ex-Senior Engineer at Google, Published 20+ research papers, Women in Tech Award 2023', 1);

-- 4. Services
INSERT INTO our_services (title, description, points, price, features, is_active) VALUES 
('Custom Software Development', 'End-to-end software development for businesses of all sizes', 'Web Applications, Mobile Apps, Cloud Solutions, API Development, Database Design', '₹2,00,000 - ₹20,00,000', 'Modern tech stack, Agile methodology, 24/7 support, 6-month warranty', 1),
('Digital Marketing Solutions', 'Complete digital marketing services to grow your online presence', 'SEO, SEM, Social Media Marketing, Content Creation, Email Marketing, Analytics', '₹50,000 - ₹5,00,000', 'Certified experts, ROI-focused campaigns, Monthly reporting, Strategy consultation', 1),
('Corporate Training Programs', 'Customized training programs for your employees and teams', 'Technical Skills, Soft Skills, Leadership Development, Digital Transformation', '₹1,00,000 - ₹10,00,000', 'Industry experts, Flexible scheduling, Certification included, Progress tracking', 1);

-- 5. Contact Submissions (sample inquiries)
INSERT INTO contact_us (full_name, email_address, subject, message, phone, status) VALUES 
('Vikash Reddy', 'vikash@techcorp.com', 'Corporate Training Inquiry', 'We are interested in upskilling our 50-member development team. Please share details about your corporate training programs.', '+91-9876543301', 'new'),
('Kavya Patel', 'kavya@startup.in', 'Full Stack Course Query', 'I want to transition from marketing to tech. Is your full stack course suitable for beginners?', '+91-9876543302', 'new'),
('Suresh Gupta', 'suresh@ecommerce.com', 'Web Development Project', 'Looking for a team to develop an e-commerce platform. Can you share your portfolio and pricing?', '+91-9876543303', 'new');

-- 6. General Testimonials
INSERT INTO testimonials (name, email, company, position, message, rating, course_name, is_featured, is_active) VALUES 
('Ankit Verma', 'ankit@techsolutions.com', 'Tech Solutions Pvt Ltd', 'Senior Developer', 'NBT transformed my career! The full stack course was comprehensive and the instructors were amazing. Got placed in a top company within 2 months.', 5, 'Full Stack Web Development', 1, 1),
('Sneha Joshi', 'sneha@datacompany.com', 'Data Insights Corp', 'Data Analyst', 'The Python and Data Science course exceeded my expectations. Real-world projects and expert guidance helped me land my dream job.', 5, 'Python Programming & Data Science', 1, 1),
('Rahul Sharma', 'rahul@digitalagency.in', 'Digital Growth Agency', 'Marketing Manager', 'Excellent digital marketing course! Practical approach and industry insights helped me start my own agency.', 4, 'Digital Marketing Mastery', 0, 1);

-- 7. Course Testimonials (with detailed course feedback)
INSERT INTO course_testimonials (name, email, course, rating, message, is_active) VALUES 
('Pooja Agarwal', 'pooja@email.com', 'Full Stack Web Development', 5, 'Outstanding course with hands-on projects. Built 5 real applications and got job-ready skills. The career support was exceptional!', 1),
('Arjun Reddy', 'arjun@email.com', 'Python Programming & Data Science', 5, 'Perfect blend of theory and practice. The machine learning modules were particularly impressive. Highly recommend!', 1),
('Madhavi Singh', 'madhavi@email.com', 'Digital Marketing Mastery', 4, 'Great course for beginners and intermediates. Learned advanced Google Ads and SEO techniques that boosted my career.', 1);

-- 8. Client Testimonials (business feedback)
INSERT INTO client_testimonials (company_name, company_email, linkedin, project_description, rating, contact_person, is_active) VALUES 
('TechStart Solutions', 'info@techstart.com', 'https://linkedin.com/company/techstart', 'Custom CRM development with React and Node.js backend. Delivered on time with excellent quality.', 5, 'Rajiv Kumar', 1),
('E-Commerce Plus', 'contact@ecommerceplus.in', 'https://linkedin.com/company/ecommerceplus', 'Complete e-commerce platform with payment gateway integration and admin panel. Exceeded expectations!', 5, 'Sunita Patel', 1),
('Digital Growth Agency', 'hello@digitalgrowth.co', 'https://linkedin.com/company/digitalgrowth', 'Comprehensive digital marketing campaign that increased our ROI by 300%. Professional and results-driven team.', 4, 'Amit Desai', 1);

-- 9. Social Media
INSERT INTO social_media (platform, platform_url, icon_class, followers, is_active, display_order) VALUES 
('Instagram', 'https://instagram.com/nbt_training', 'fab fa-instagram', 25000, 1, 1),
('Facebook', 'https://facebook.com/nbttraining', 'fab fa-facebook-f', 18000, 1, 2),
('LinkedIn', 'https://linkedin.com/company/nbt-training', 'fab fa-linkedin-in', 15000, 1, 3),
('YouTube', 'https://youtube.com/c/nbttraining', 'fab fa-youtube', 12000, 1, 4),
('Twitter', 'https://twitter.com/nbt_training', 'fab fa-twitter', 8000, 1, 5);

-- 10. Client Projects
INSERT INTO client (client_name, company_name, contact_email, phone, task, duration, status, budget, notes) VALUES 
('Vikram Patel', 'FinTech Innovations', 'vikram@fintech.com', '+91-9876543401', 'Mobile banking app development with security features', '6 months', 'active', '₹15,00,000', 'High priority project, requires regular updates'),
('Deepika Reddy', 'Healthcare Solutions', 'deepika@healthcare.in', '+91-9876543402', 'Hospital management system with patient portal', '4 months', 'active', '₹8,00,000', 'Integration with existing systems required'),
('Karan Singh', 'Retail Chain Corp', 'karan@retailchain.com', '+91-9876543403', 'Inventory management and POS system', '3 months', 'completed', '₹5,00,000', 'Successful deployment across 20 stores');

-- 11. Coupons/Discounts
INSERT INTO coupons (code, description, discount, time_limit, usage_limit, is_active) VALUES 
('WELCOME2025', 'New Year Special - 25% off on all courses', 25.00, '2025-03-31', 100, 1),
('STUDENT50', 'Student Discount - 50% off for college students', 50.00, '2025-12-31', 200, 1),
('CORPORATE20', 'Corporate Training Discount - 20% off for teams of 10+', 20.00, '2025-06-30', 50, 1),
('EARLYBIRD15', 'Early Bird Offer - 15% off for advance bookings', 15.00, '2025-04-30', 150, 1);

-- 12. Overview Images (gallery/showcase)
INSERT INTO overview_images (title, description, image_sequence, image_name, category, is_active) VALUES 
('Modern Learning Environment', 'State-of-the-art classrooms with latest technology and comfortable seating', 1, 'classroom-1.jpg', 'facilities', 1),
('Hands-on Lab Sessions', 'Dedicated computer labs with high-end workstations for practical learning', 2, 'computer-lab.jpg', 'facilities', 1),
('Student Success Stories', 'Our alumni working at top tech companies worldwide', 3, 'success-stories.jpg', 'achievements', 1),
('Industry Expert Sessions', 'Regular workshops and seminars by industry professionals', 4, 'expert-session.jpg', 'events', 1),
('Project Showcase', 'Students presenting their capstone projects to industry panels', 5, 'project-demo.jpg', 'projects', 1);

SELECT 'Enhanced sample data inserted successfully!' as status;
SELECT 'Data Summary:' as info;
SELECT 'Courses' as table_name, COUNT(*) as records FROM courses
UNION ALL SELECT 'Team Members', COUNT(*) FROM meet_our_team
UNION ALL SELECT 'Founders', COUNT(*) FROM founder_card  
UNION ALL SELECT 'Services', COUNT(*) FROM our_services
UNION ALL SELECT 'Contacts', COUNT(*) FROM contact_us
UNION ALL SELECT 'Testimonials', COUNT(*) FROM testimonials
UNION ALL SELECT 'Course Reviews', COUNT(*) FROM course_testimonials
UNION ALL SELECT 'Client Reviews', COUNT(*) FROM client_testimonials
UNION ALL SELECT 'Social Media', COUNT(*) FROM social_media
UNION ALL SELECT 'Active Projects', COUNT(*) FROM client
UNION ALL SELECT 'Coupons', COUNT(*) FROM coupons
UNION ALL SELECT 'Gallery Images', COUNT(*) FROM overview_images;
