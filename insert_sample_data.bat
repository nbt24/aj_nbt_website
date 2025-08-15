@echo off
echo Inserting sample data into NBT database...
echo.

cd /d C:\xampp\mysql\bin
.\mysql.exe -u root < "c:\xampp\htdocs\nbt\aj_nbt_website\insert_sample_data.sql"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ SUCCESS: Sample data has been inserted into all tables!
    echo.
    echo You can now check your frontend interface to see how the data looks.
    echo Admin login: admin@nbt.com / admin123
    echo.
) else (
    echo.
    echo ❌ ERROR: Failed to insert sample data. Please check the error messages above.
    echo.
)

pause
