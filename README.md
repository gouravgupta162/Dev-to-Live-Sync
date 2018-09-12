# Dev-to-Live-Sync
This project can sync your dev/staging server files to live/production server. 

# Dev/Staging Server

# IMPORTANT NOTE - 
$SECRET = Specific string which should need to be same as on Production servers
$PATH = This is a absolute path of Staging server directory which you want to sync with production server. (/home/######/public_html/staging)

Readme.html is just a file which will sync with production server and test script.

# Live/Production Server

# IMPORTANT NOTE - 
$SECRET = Specific string which should need to be same on both servers (Staging and Production)
$PATH = This is a absolute path of Production server directory where you want to sync with staging server. (/home/######/public_html/production)
$URL  = 'https://domain-name.com/staging/stagingsyncer.php' (URL path of staging server)
