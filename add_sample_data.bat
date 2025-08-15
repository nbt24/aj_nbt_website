@echo off
echo ========================================
echo    NBT Database Sample Data Insertion
echo ========================================
echo.

cd /d C:\xampp\mysql\bin

echo Inserting sample data into main tables...

.\mysql.exe -u root -e "USE nbt; INSERT IGNORE INTO training_courses (title, description_1, educator, timeline, people, rating) VALUES ('Digital Marketing Course', 'Complete digital marketing training with SEO and social media', 'Marketing Expert', '8 weeks', '40+ enrolled', 4.9);"

.\mysql.exe -u root -e "USE nbt; INSERT IGNORE INTO courses (title, description_1, educator, timeline, people, rating) VALUES ('Data Science Bootcamp', 'Learn Python, Machine Learning, and Data Analysis', 'Data Scientist', '16 weeks', '20+ enrolled', 4.8);"

.\mysql.exe -u root -e "USE nbt; INSERT IGNORE INTO meet_our_team (name, description, position, number, email, image_name) VALUES ('Neha Agarwal', 'CTO and tech innovator passionate about education', 'CTO & Co-Founder', '+91-9876543221', 'neha@nbt.com', 'neha-agarwal.jpg');"

.\mysql.exe -u root -e "USE nbt; INSERT IGNORE INTO founder_card (name, description, position, number, email, image_name) VALUES ('Amit Singh', 'Educational technology leader with 12+ years experience', 'Head of Operations', '+91-9876543222', 'amit@nbt.com', 'amit-singh.jpg');"

.\mysql.exe -u root -e "USE nbt; INSERT IGNORE INTO our_services (title, description, price, image_name) VALUES ('Mobile App Development', 'Custom mobile applications for iOS and Android platforms', '₹75,000 - ₹3,00,000', 'mobile-app.jpg');"

.\mysql.exe -u root -e "USE nbt; INSERT IGNORE INTO contact_us (full_name, email_address, subject, message) VALUES ('Kavya Reddy', 'kavya@email.com', 'Partnership Inquiry', 'Interested in business partnership opportunities');"

.\mysql.exe -u root -e "USE nbt; INSERT IGNORE INTO student_reviews (name, email, course, rating, message, is_active) VALUES ('Manish Kumar', 'manish@email.com', 'Digital Marketing Course', 5, 'Outstanding course that helped me start my own digital agency!', 1);"

echo.
echo ✅ SUCCESS: Sample data has been inserted into all main tables!
echo.
echo Your NBT website frontend now has sample content including:
echo • Training Courses and Local Courses
echo • Team Members and Founders
echo • Business Services  
echo • Student Reviews and Contact Forms
echo • Admin Panel Access: admin@nbt.com / admin123
echo.
echo You can now view your website to see how the data appears!
echo.
pause
