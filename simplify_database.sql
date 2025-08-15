-- NBT Database Simplification Script
-- Keeps only essential tables for non-tech team management
-- Removes duplicate and complex tables

USE nbt;

-- Backup important data first (optional - commented out)
-- CREATE TABLE backup_courses AS SELECT * FROM courses;
-- CREATE TABLE backup_testimonials AS SELECT * FROM testimonials;

-- Drop unnecessary duplicate tables (keep the simpler versions)
DROP TABLE IF EXISTS administrators;  -- Keep 'admin' instead
DROP TABLE IF EXISTS training_courses; -- Keep 'courses' instead  
DROP TABLE IF EXISTS team_members;     -- Keep 'meet_our_team' instead
DROP TABLE IF EXISTS business_services; -- Keep 'our_services' instead
DROP TABLE IF EXISTS contact_submissions; -- Keep 'contact_us' instead

-- Drop complex client/project management tables (not needed for simple website)
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS client_projects;
DROP TABLE IF EXISTS client_reviews;
DROP TABLE IF EXISTS client_testimonials;

-- Drop duplicate company info tables
DROP TABLE IF EXISTS company_founders; -- Keep 'meet_our_team' for founders too
DROP TABLE IF EXISTS founder_card;     -- Keep 'meet_our_team' for founders too
DROP TABLE IF EXISTS company_mission;  -- Keep 'our_mission' instead
DROP TABLE IF EXISTS company_testimonials; -- Keep 'testimonials' instead

-- Drop marketing/promotional tables (keep it simple)
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS discount_codes;
DROP TABLE IF EXISTS hero_carousel_images;
DROP TABLE IF EXISTS overview_images;

-- Drop duplicate social media tables
DROP TABLE IF EXISTS social_media_links; -- Keep 'social_media' instead

-- Drop complex review systems (keep simple testimonials)
DROP TABLE IF EXISTS student_reviews;
DROP TABLE IF EXISTS course_testimonials; -- Keep 'testimonials' instead

-- Show remaining essential tables
SELECT 'Database simplified! Remaining essential tables:' as status;
SHOW TABLES;

-- Show row counts in remaining tables
SELECT 'admin' as table_name, COUNT(*) as rows FROM admin
UNION ALL SELECT 'courses', COUNT(*) FROM courses
UNION ALL SELECT 'meet_our_team', COUNT(*) FROM meet_our_team  
UNION ALL SELECT 'our_services', COUNT(*) FROM our_services
UNION ALL SELECT 'contact_us', COUNT(*) FROM contact_us
UNION ALL SELECT 'testimonials', COUNT(*) FROM testimonials
UNION ALL SELECT 'our_mission', COUNT(*) FROM our_mission
UNION ALL SELECT 'social_media', COUNT(*) FROM social_media;
