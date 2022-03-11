<?php 
/**
 * User configurable settings
 * Modify these variables to change functionality
 */

// Relative archive folder
define('PHOTO_ARCHIVE_DIR', 'archive');

// Enable database connection
define('PHOTO_ARCHIVE_USE_DB', true);
// Database settings
define('PHOTO_ARCHIVE_DB_HOST', 'localhost');
define('PHOTO_ARCHIVE_DB_USER', '');
define('PHOTO_ARCHIVE_DB_PASSWORD', '');
define('PHOTO_ARCHIVE_DB_DATABASE', '');

// Max thumb dimensions
define('THUMB_X', '192');
define('THUMB_Y', '108');

// Max mid image dimensions
define('MID_X', '1920');
define('MID_Y', '1080');