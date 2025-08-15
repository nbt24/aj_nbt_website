@echo off
echo ========================================
echo    NBT Database Simplification
echo ========================================
echo.
echo This will simplify your database to keep only essential tables
echo for easy management by non-tech team members.
echo.
echo ESSENTIAL TABLES TO KEEP:
echo • admin           - Admin panel access
echo • courses         - Course offerings  
echo • meet_our_team   - Team member profiles
echo • our_services    - Service offerings
echo • contact_us      - Contact form submissions
echo • testimonials    - Customer reviews
echo • our_mission     - Company mission/about
echo • social_media    - Social media links
echo.
echo REMOVING: Duplicate tables, complex client management,
echo           marketing tables, and redundant systems
echo.
set /p confirm="Continue with simplification? (Y/N): "
if /i "%confirm%" NEQ "Y" (
    echo Operation cancelled.
    pause
    exit /b
)

echo.
echo Simplifying database...

cd /d C:\xampp\mysql\bin
.\mysql.exe -u root < "c:\xampp\htdocs\nbt\aj_nbt_website\simplify_database.sql"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ SUCCESS: Database simplified successfully!
    echo.
    echo Your NBT website now has only 8 essential tables:
    echo ✓ admin, courses, meet_our_team, our_services
    echo ✓ contact_us, testimonials, our_mission, social_media  
    echo.
    echo This makes it much easier for your non-tech team to:
    echo • Add/edit courses
    echo • Manage team member profiles  
    echo • Update service offerings
    echo • View contact form submissions
    echo • Manage customer testimonials
    echo • Update company information
    echo.
    echo Admin Access: admin@nbt.com / admin123
    echo.
) else (
    echo.
    echo ❌ ERROR: Failed to simplify database.
    echo.
)

pause
