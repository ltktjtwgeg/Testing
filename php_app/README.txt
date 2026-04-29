=== Setup Steps for Hostinger ===

1. Login to your Hostinger setup.
2. Go to "Databases" and create a new MySQL Database. Note down the Database Name, Username, and Password.
3. Open `config.php` and update it with these details:
   $host = 'localhost'; // Usually localhost on Hostinger
   $db_name = 'u918090917_Jalwa_369';
   $username = 'u918090917_Jalwa_369';
   $password = 'your_db_password';
4. Go to PHPMyAdmin in Hostinger. Select your newly created database.
5. Click on the "Import" tab and upload `database.sql` provided in this folder. Execute it.
6. Upload all files (excluding this README and database.sql) replacing `index.html` inside your `public_html` folder using Hostinger's File Manager. (Make sure you upload contents of php_app, not the folder itself)
7. Create a folder named `uploads` in `public_html` and set its permissions to 777 (so PHP can write screenshots into it).
8. Go to your domain. You can login with username: 'admin', password: 'admin123'. 
9. Go to Admin Panel -> Settings to set your UPI address.

That's it! Your Aviator clone is live.
