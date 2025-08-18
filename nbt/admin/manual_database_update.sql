-- Manual SQL commands to update coupons table structure
-- Run these commands in phpMyAdmin if you prefer manual database update

-- Step 1: Add the end_date column
ALTER TABLE coupons ADD COLUMN end_date DATE NULL AFTER discount;

-- Step 2: Convert existing time_limit data to end_date (optional)
-- This updates existing coupons by adding the time_limit days to current date
UPDATE coupons 
SET end_date = DATE_ADD(NOW(), INTERVAL time_limit DAY) 
WHERE time_limit IS NOT NULL AND time_limit > 0;

-- Step 3: Verify the structure
SHOW COLUMNS FROM coupons;
