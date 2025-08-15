-- Update pricing and other field issues in optimized database
USE nbt;

-- Fix services pricing
UPDATE our_services SET 
    price = 'Starting at $999',
    points = 'Custom website design|Responsive layouts|SEO optimization|Performance tuning|Cross-browser compatibility'
WHERE id = 1;

UPDATE our_services SET 
    price = 'Starting at $1499',
    points = 'Data visualization dashboards|Analytics reporting|KPI tracking systems|Database optimization|Machine learning insights'
WHERE id = 2;

UPDATE our_services SET 
    price = 'Starting at $799',
    points = 'Social media management|Content creation strategy|SEO optimization|PPC campaign management|Email marketing automation'
WHERE id = 3;

-- Add some additional essential data
INSERT INTO contact_us (full_name, email_address, subject, message, status) VALUES 
('John Doe', 'john@example.com', 'Course Inquiry', 'I am interested in the Web Development Bootcamp. Can you provide more details?', 'new'),
('Jane Smith', 'jane@example.com', 'Business Consultation', 'Looking for digital marketing services for my startup.', 'new'),
('Mike Johnson', 'mike@example.com', 'Partnership', 'Interested in exploring partnership opportunities.', 'read');

-- Add client testimonials
INSERT INTO client_testimonials (company_name, company_email, project_description, rating) VALUES 
('TechStart Inc', 'contact@techstart.com', 'Complete website redesign and digital marketing campaign', 5),
('DataFlow Solutions', 'info@dataflow.com', 'Business intelligence dashboard development', 5),
('GrowthCorp', 'hello@growthcorp.com', 'Digital transformation consulting', 4);

-- Add course testimonials
INSERT INTO course_testimonials (name, email, course, rating, message) VALUES 
('Alex Chen', 'alex@example.com', 'Web Development Bootcamp', 5, 'Excellent course! The instructors are knowledgeable and the projects are practical.'),
('Sarah Williams', 'sarah@example.com', 'Data Science & Analytics', 5, 'This course changed my career. Now working as a data analyst at a Fortune 500 company.'),
('David Brown', 'david@example.com', 'Digital Marketing Mastery', 4, 'Great course content and practical assignments. Highly recommended!');

SELECT 'Data updated successfully!' as status;

-- Verify updates
SELECT 'SERVICES:' as section;
SELECT id, title, price FROM our_services;

SELECT 'COURSES:' as section; 
SELECT id, title, type, price FROM courses LIMIT 3;

SELECT 'TESTIMONIALS:' as section;
SELECT COUNT(*) as total_testimonials FROM testimonials;
SELECT COUNT(*) as course_testimonials FROM course_testimonials;
SELECT COUNT(*) as client_testimonials FROM client_testimonials;
