YouTube Channel Viewer App
- This is a web application that allows users to view and sync YouTube channel videos using the YouTube Data API v3 and Google OAuth authentication. The app is built with PHP, MySQL, and runs locally using Laragon.

Table of Contents
1. Project Setup
2. Database Setup
3. Configuration
  * YouTube API Key
  * Google OAuth Credentials
4. Running the Application
5. Entry Point / URL

Project Setup
1. Make sure you have Laragon installed on your Windows machine. You can download it here: https://laragon.org/
2. Clone or download this repository to your local machine: git clone https://github.com/your-username/youtube-channel-app.git
3. Move the project folder to Laragon's www directory. For example: C:\laragon\www\youtube-channel-app
4. Start Laragon and make sure Apache and MySQL are running.
5. Open Laragon’s terminal (or CMD) and navigate to the project folder: cd C:\laragon\www\youtube-channel-app

Dependencies / Libraries
1. Install the required PHP dependencies using Composer: composer install
2. Install the Google API Client for PHP, which is required for OAuth login and fetching YouTube data: composer require google/apiclient
3. Make sure Composer is installed and added to your system PATH.

Database Setup
1. Open HeidiSQL or phpMyAdmin (included in Laragon) and create a new database: CREATE DATABASE youtube_app;
2. Import the provided SQL file (youtube_app.sql) into the database:
  * Open HeidiSQL → Select the database → Click Import → Choose the SQL file → Execute.
3. Update the database configuration in config.php:
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'youtube_app');
    define('DB_USER', 'root');
    define('DB_PASS', ''); // default Laragon password is empty


Configuration - YouTube API Key
1. Go to Google Cloud Console
2. Create a new project (or select an existing one).
3. Navigate to APIs & Services → Credentials.
4. Click Create Credentials → API Key.
5. Copy the API Key and add it to your config.php file:
  * define('YOUTUBE_API_KEY', 'YOUR_YOUTUBE_API_KEY_HERE');
  * Make sure the YouTube Data API v3 is enabled in your Google Cloud project.


Google OAuth Credentials
1. In Google Cloud Console, go to APIs & Services → Credentials → Create Credentials → OAuth Client ID.
2. Choose Web Application.
3. Set the Authorized redirect URI to: http://youtube-channel-app.test/auth/callback.php
4. Copy the Client ID and Client Secret and update config.php:
  define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID_HERE');
  define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET_HERE');
  define('GOOGLE_REDIRECT_URI', 'http://youtube-channel-app.test/auth/callback.php');


Running the Application
1. Start Laragon and ensure Apache & MySQL are running.
2. Open your browser and navigate to: http://youtube-channel-app.test/login.php
3. Login using Google OAuth.
4. Enter a YouTube channel ID to sync and view videos.


Entry Point / URL
1. Local URL: http://youtube-channel-app.test/login.php
2. Sync & View Videos: After login, use the main dashboard to enter a channel ID.


Notes / Troubleshooting
* Ensure your hosts file contains an entry for youtube-channel-app.test pointing to 127.0.0.1.
* Make sure your database credentials in config.php match your Laragon MySQL setup.
* If the OAuth consent screen shows errors, verify your app name and scopes in Google Cloud Console.
* For JSON or fetch errors, check browser console and ensure API key and channel IDs are valid.