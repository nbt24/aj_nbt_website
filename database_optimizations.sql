-- NBT Database Optimization Scripts
-- Run these in phpMyAdmin or MySQL command line

-- 1. Add missing indexes for better performance
ALTER TABLE courses ADD INDEX idx_rating (rating);
ALTER TABLE courses ADD INDEX idx_educator (educator);
ALTER TABLE client_testimonials ADD INDEX idx_active (is_active);
ALTER TABLE client_testimonials ADD INDEX idx_rating (rating);
ALTER TABLE course_testimonials ADD INDEX idx_active (is_active);
ALTER TABLE course_testimonials ADD INDEX idx_rating (rating);
ALTER TABLE overview_images ADD INDEX idx_sequence (image_sequence);
ALTER TABLE meet_our_team ADD INDEX idx_sequence (image_sequence);
ALTER TABLE founder_card ADD INDEX idx_sequence (image_sequence);

-- 2. Add constraints for data integrity
ALTER TABLE course_testimonials ADD CONSTRAINT chk_course_rating CHECK (rating BETWEEN 1 AND 5);
ALTER TABLE client_testimonials ADD CONSTRAINT chk_client_rating CHECK (rating BETWEEN 1 AND 5);
ALTER TABLE testimonials ADD CONSTRAINT chk_testimonial_rating CHECK (rating BETWEEN 1 AND 5);

-- 3. Add missing is_active column to course_testimonials if not exists
ALTER TABLE course_testimonials ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT 1;

-- 4. Add missing is_active column to client_testimonials if not exists
ALTER TABLE client_testimonials ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT 1;

-- 5. Optimize VARCHAR lengths (adjust as needed)
-- ALTER TABLE our_mission MODIFY students VARCHAR(50);
-- ALTER TABLE our_mission MODIFY courses VARCHAR(50);
-- ALTER TABLE our_mission MODIFY success_rate VARCHAR(50);

-- 6. Add created_at to tables that might be missing it
ALTER TABLE client ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- 7. Create backup table structure (run before migration)
-- CREATE TABLE courses_backup AS SELECT * FROM courses;
-- CREATE TABLE client_testimonials_backup AS SELECT * FROM client_testimonials;
-- CREATE TABLE course_testimonials_backup AS SELECT * FROM course_testimonials;

-- 8. For Hostinger migration - convert LONGBLOB to file paths
-- WARNING: Run data migration script first to save files
-- ALTER TABLE courses MODIFY image VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE meet_our_team MODIFY image_data VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE founder_card MODIFY image_data VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE our_services MODIFY image_data VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE overview_images MODIFY image_data VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE course_testimonials MODIFY image VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE course_testimonials MODIFY video VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE client_testimonials MODIFY company_logo VARCHAR(255) DEFAULT NULL;
