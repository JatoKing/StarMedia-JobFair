<?php
// Application-wide configuration for StarMedia Job Fair

date_default_timezone_set('Asia/Kuala_Lumpur');

// General app info
define('APP_NAME', 'StarMedia Job Fair');
define('APP_URL', 'http://starmedia-jobfair.test');
define('APP_ENV', 'development'); // development | production

// Job Fair event details - digunakan untuk Countdown Timer feature
define('FAIR_START_DATE', '2026-09-15 09:00:00');
define('FAIR_END_DATE', '2026-09-15 18:00:00');
define('FAIR_VENUE', 'Kuala Lumpur Convention Centre');

// Database config (rujukan sahaja - actual connection dalam includes/db.php)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'starmedia_jobfair_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// CORS allowed origins (untuk development, Vue dev server)
define('ALLOWED_ORIGINS', [
    'http://localhost:5173',
    'http://starmedia-jobfair.test'
]);

// Error reporting - matikan display_errors bila production
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}