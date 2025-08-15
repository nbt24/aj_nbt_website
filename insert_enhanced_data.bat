@echo off
echo =========================================
echo    NBT Enhanced Sample Data Insertion
echo =========================================
echo.
echo This will populate your enhanced database with comprehensive
echo sample data for all tables, making your website fully functional.
echo.
echo SAMPLE DATA INCLUDES:
echo • 3 Comprehensive Courses (Full Stack, Python, Digital Marketing)
echo • 3 Team Members with detailed profiles
echo • 2 Founder profiles with achievements
echo • 3 Service offerings with pricing
echo • 3 Sample contact inquiries
echo • 3 Student testimonials (featured)
echo • 3 Course reviews with ratings
echo • 3 Client testimonials from businesses
echo • 5 Social media platforms with follower counts
echo • 3 Active client projects
echo • 4 Discount coupons/promotions
echo • 5 Gallery images for showcase
echo.
echo Total: 40+ realistic data records across all tables
echo.
set /p confirm="Insert comprehensive sample data? (Y/N): "
if /i "%confirm%" NEQ "Y" (
    echo Operation cancelled.
    pause
    exit /b
)

echo.
echo Inserting enhanced sample data...

cd /d C:\xampp\mysql\bin
.\mysql.exe -u root < "c:\xampp\htdocs\nbt\aj_nbt_website\insert_enhanced_data.sql"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ SUCCESS: Enhanced sample data inserted!
    echo.
    echo 🎯 YOUR NBT WEBSITE IS NOW FULLY FUNCTIONAL WITH:
    echo.
    echo 📚 COURSE CATALOG
    echo   • Full Stack Web Development ₹45,000 (4.8★)
    echo   • Python ^& Data Science ₹35,000 (4.9★)  
    echo   • Digital Marketing ₹25,000 (4.7★)
    echo.
    echo 👥 TEAM PROFILES
    echo   • Sarah Johnson - Technical Lead
    echo   • Dr. Rajesh Kumar - Data Science Head
    echo   • Priya Sharma - Marketing Head
    echo.
    echo 🏢 LEADERSHIP
    echo   • Amit Agarwal - CEO ^& Founder
    echo   • Neha Singh - CTO ^& Co-Founder
    echo.
    echo 💼 BUSINESS SERVICES
    echo   • Software Development
    echo   • Digital Marketing Solutions  
    echo   • Corporate Training Programs
    echo.
    echo 📞 CUSTOMER INTERACTION
    echo   • Contact form submissions
    echo   • Student testimonials ^& reviews
    echo   • Client project testimonials
    echo.
    echo 🎁 MARKETING FEATURES
    echo   • Discount coupons (WELCOME2025, STUDENT50)
    echo   • Social media integration (25K+ followers)
    echo   • Gallery showcase images
    echo.
    echo 🔐 Admin Panel: admin@nbt.com / password123
    echo 🌐 Website ready for production deployment!
    echo.
) else (
    echo.
    echo ❌ ERROR: Failed to insert sample data.
    echo.
)

pause
