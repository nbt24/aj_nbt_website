@echo off
echo Inserting essential sample data into NBT database...
echo.

cd /d C:\xampp\mysql\bin
.\mysql.exe -u root < "c:\xampp\htdocs\nbt\aj_nbt_website\insert_essential_data.sql"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ SUCCESS: Essential sample data has been inserted!
    echo.
    echo Your frontend should now display sample content.
    echo Admin login: admin@nbt.com / admin123
    echo.
    echo Sample data includes:
    echo - 2 Courses (Full Stack & Python)
    echo - 2 Team Members (Sarah & Rohit)
    echo - 2 Founders (Rajesh & Neha)
    echo - 2 Services (Software Dev & Digital Marketing)
    echo - Social Media Links
    echo - Student Reviews and Testimonials
    echo - Contact Forms and Discount Codes
    echo.
) else (
    echo.
    echo ❌ ERROR: Failed to insert sample data. Please check the error messages above.
    echo.
)

pause
