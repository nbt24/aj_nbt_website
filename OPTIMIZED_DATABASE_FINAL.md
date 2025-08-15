# NBT Website - Optimized Database Implementation

## Overview
This document outlines the optimized database structure created for the NBT (Next Big Tech) website, focusing on the most relevant fields required by the actual codebase.

## Database Summary
- **Database Name**: `nbt`
- **Total Tables**: 14 optimized tables
- **Focus**: Essential fields for frontend display and admin management
- **Features**: Image storage, pricing, ratings, status tracking

## Optimized Table Structure

### 1. Admin Management
```sql
admin
├── id (Primary Key)
├── email (Unique login)
├── password 
├── name
└── created_at
```

### 2. Course Management
```sql
courses
├── id (Primary Key)
├── title ⭐
├── image (LONGBLOB) ⭐
├── type (Online/Hybrid) ⭐
├── description_1 ⭐
├── description_2 ⭐
├── educator ⭐
├── timeline ⭐
├── people ⭐
├── rating (Decimal) ⭐
├── price (NEW - Added) ⭐
├── link
├── is_active
└── created_at
```

### 3. Team Members
```sql
meet_our_team
├── id (Primary Key)
├── name ⭐
├── position ⭐
├── description ⭐
├── image_sequence
├── linkedin ⭐
├── email
├── phone
├── image_name ⭐
├── image_type ⭐
├── image_size
├── image_data (LONGBLOB) ⭐
├── is_active
└── created_at
```

### 4. Founder Profiles
```sql
founder_card
├── id (Primary Key)
├── name ⭐
├── position ⭐
├── description ⭐
├── image_sequence
├── linkedin ⭐
├── email
├── achievements
├── image_name ⭐
├── image_type ⭐
├── image_size
├── image_data (LONGBLOB) ⭐
├── is_active
└── created_at
```

### 5. Service Offerings
```sql
our_services
├── id (Primary Key)
├── title ⭐
├── description ⭐
├── points ⭐
├── price ⭐
├── image_name ⭐
├── image_type ⭐
├── image_size
├── image_data (LONGBLOB) ⭐
├── is_active
└── created_at
```

### 6. Contact Management
```sql
contact_us
├── id (Primary Key)
├── full_name ⭐
├── email_address ⭐
├── subject ⭐
├── message ⭐
├── phone
├── status (new/read/replied/closed) ⭐
├── admin_notes
└── created_at
```

### 7. General Testimonials
```sql
testimonials
├── id (Primary Key)
├── name ⭐
├── email
├── message ⭐
├── rating ⭐
├── course_name
├── company
├── is_featured
├── is_active
└── created_at
```

### 8. Course-Specific Reviews
```sql
course_testimonials
├── id (Primary Key)
├── name ⭐
├── email
├── course ⭐
├── rating ⭐
├── message ⭐
├── image (LONGBLOB)
├── video (LONGBLOB)
├── is_active
└── created_at
```

### 9. Client Business Reviews
```sql
client_testimonials
├── id (Primary Key)
├── company_name ⭐
├── company_email
├── linkedin
├── project_description ⭐
├── rating ⭐
├── company_logo (LONGBLOB) ⭐
├── is_active
└── created_at
```

### 10. Company Mission
```sql
our_mission
├── id (Primary Key)
├── title ⭐
├── description ⭐
├── students (e.g., "1000+") ⭐
├── courses (e.g., "15+") ⭐
├── success_rate (e.g., "95%") ⭐
└── created_at
```

### 11. Social Media Links
```sql
social_media
├── id (Primary Key)
├── platform ⭐
├── followers ⭐
├── platform_url ⭐
├── is_active
└── created_at
```

### 12. Client Project Management
```sql
client
├── id (Primary Key)
├── client_name ⭐
├── company_name ⭐
├── contact_email
├── task ⭐
├── duration ⭐
├── link
├── status (active/completed/pending/cancelled) ⭐
├── notes
└── created_at
```

### 13. Discount System
```sql
coupons
├── id (Primary Key)
├── code (Unique) ⭐
├── description ⭐
├── discount (Decimal) ⭐
├── time_limit (Date) ⭐
├── is_active
└── created_at
```

### 14. Image Gallery
```sql
overview_images
├── id (Primary Key)
├── title
├── image_sequence ⭐
├── image_name ⭐
├── image_type ⭐
├── image_size
├── image_data (LONGBLOB) ⭐
├── is_active
└── created_at
```

## Sample Data Summary

### Courses (3 records)
- Complete Web Development Bootcamp ($499.00)
- Data Science & Analytics ($699.00)
- Digital Marketing Mastery ($399.00)

### Services (3 records)
- Web Development (Starting at $999)
- Business Intelligence (Starting at $1499)
- Digital Marketing (Starting at $799)

### Team Members (3 records)
- Sarah Johnson (Lead Developer)
- Michael Chen (Data Science Instructor)
- Emily Rodriguez (Marketing Director)

### Testimonials
- General: 3 records
- Course-specific: 3 records
- Client testimonials: 3 records

## Code Integration Updates

### ✅ Updated Files
1. **manage_courses.php**
   - Added `price` field to both add and edit forms
   - Updated SQL queries to include price handling
   - Fixed duplicate execute statements

### ✅ Working Admin Panel Modules
1. manage_courses.php ✅
2. manage_services.php ✅
3. manage_team.php ✅
4. manage_testimonials.php ✅
5. manage_mission.php ✅
6. manage_contacts.php ✅
7. manage_overview_images.php ✅
8. manage_social_media.php ✅
9. coupons.php ✅
10. company_list.php ✅
11. course_testimonials_admin.php ✅
12. admin_clients.php ✅
13. admin_founders.php ✅

### ⭐ Key Field Mapping (Frontend → Database)
Based on frontend analysis:

**Services Display (index.php lines 720-750)**:
- `image_type` → `image_type`
- `image_data` → `image_data`
- `title` → `title`
- `description` → `description`
- `points` → `points` (pipe-separated list)

**Courses Display (index.php lines 1025-1070)**:
- `type` → `type`
- `educator` → `educator`
- `timeline` → `timeline`
- `people` → `people`
- `rating` → `rating`
- `price` → `price` (NEW field added)

**Team Display (index.php lines 640-670)**:
- `image_data` → `image_data`
- `name` → `name`
- `position` → `position`
- `linkedin` → `linkedin`

## Performance Optimizations
- **Image Storage**: LONGBLOB for direct binary storage
- **Status Fields**: ENUM types for controlled values
- **Indexing**: Primary keys and essential lookups
- **Field Types**: Optimized for actual usage patterns

## Database Connection
- **Host**: localhost
- **Database**: nbt
- **User**: root
- **Password**: (empty)
- **Connection File**: `nbt/config/db.php`

## File Locations
- **Database Scripts**: `c:\xampp\htdocs\nbt\aj_nbt_website\`
- **Website Code**: `c:\xampp\htdocs\nbt\aj_nbt_website\nbt\`
- **Admin Panel**: `c:\xampp\htdocs\nbt\aj_nbt_website\nbt\admin\`
- **Configuration**: `c:\xampp\htdocs\nbt\aj_nbt_website\nbt\config\`

## Verification Commands
```sql
-- Check table structure
SHOW TABLES;

-- Verify sample data
SELECT COUNT(*) FROM courses;
SELECT COUNT(*) FROM our_services;
SELECT COUNT(*) FROM meet_our_team;

-- Test key queries
SELECT title, price FROM courses;
SELECT title, price FROM our_services;
SELECT name, position FROM meet_our_team;
```

## Status: ✅ COMPLETE
The optimized database structure is now implemented with:
- All 14 essential tables created
- Sample data populated (40+ records)
- Admin panel integration working
- Frontend display fields aligned
- Price management added to courses
- Enhanced data validation and status tracking

**Ready for production use with full admin panel functionality!**
