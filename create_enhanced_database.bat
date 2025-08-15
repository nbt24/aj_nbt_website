@echo off
echo =========================================
echo    NBT Enhanced Database Creation
echo =========================================
echo.
echo This will create all tables required by your website code
echo with enhanced features and proper column structures.
echo.
echo NEW ENHANCED FEATURES:
echo • Password hashing for admin security
echo • Image storage (LONGBLOB) for all media
echo • Status tracking for contacts and testimonials  
echo • Course and client management systems
echo • Social media integration with follower counts
echo • Coupon/discount code system
echo • Gallery/overview image management
echo • Enhanced testimonial system (course + client)
echo • Project timeline and budget tracking
echo.
set /p confirm="Create enhanced database structure? (Y/N): "
if /i "%confirm%" NEQ "Y" (
    echo Operation cancelled.
    pause
    exit /b
)

echo.
echo Creating enhanced database structure...

cd /d C:\xampp\mysql\bin
.\mysql.exe -u root < "c:\xampp\htdocs\nbt\aj_nbt_website\create_enhanced_database.sql"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ SUCCESS: Enhanced NBT database created!
    echo.
    echo 📊 DATABASE TABLES CREATED:
    echo ┌─────────────────────────────────────────────┐
    echo │ CORE TABLES                                 │
    echo │ • admin - Enhanced admin system             │
    echo │ • courses - Full course management          │
    echo │ • meet_our_team - Team member profiles      │
    echo │ • founder_card - Leadership showcase        │
    echo │ • our_services - Service offerings          │
    echo │ • our_mission - Company information         │
    echo │                                             │
    echo │ CUSTOMER INTERACTION                        │
    echo │ • contact_us - Contact form management      │
    echo │ • testimonials - General testimonials       │
    echo │ • course_testimonials - Course reviews      │
    echo │ • client_testimonials - Business reviews    │
    echo │                                             │
    echo │ BUSINESS FEATURES                           │
    echo │ • client - Project management               │
    echo │ • coupons - Discount system                 │
    echo │ • social_media - Social integration         │
    echo │ • overview_images - Gallery management      │
    echo └─────────────────────────────────────────────┘
    echo.
    echo 🔐 Admin Access: admin@nbt.com / password123
    echo 💡 All tables support image uploads and status tracking
    echo 🚀 Your website now has full functionality!
    echo.
) else (
    echo.
    echo ❌ ERROR: Failed to create enhanced database.
    echo.
)

pause
