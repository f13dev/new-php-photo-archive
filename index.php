<?php namespace F13Dev\PhotoArchive;

@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) {
    ob_end_flush();
}
ob_implicit_flush(1);

define('PHOTO_ARCHIVE_PATH', dirname(__FILE__));
define('PHOTO_ARCHIVE_FOLDER', PHOTO_ARCHIVE_PATH.'/archive/');

define('PHOTO_ARCHIVE_URL', (isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(__DIR__)).'/');
define('PHOTO_ARCHIVE_AJAX', PHOTO_ARCHIVE_URL.'?ajax=true&');
define('PHOTO_ARCHIVE_FOLDER_URL', PHOTO_ARCHIVE_URL.'archive/');

define('PHOTO_ARCHIVE_IMAGES_URL', PHOTO_ARCHIVE_FOLDER_URL.'images');
define('PHOTO_ARCHIVE_MID_URL', PHOTO_ARCHIVE_FOLDER_URL.'mid/');
define('PHOTO_ARCHIVE_THUMB_URL', PHOTO_ARCHIVE_FOLDER_URL.'thumbs/');

define('PHOTO_ARCHIVE_IMAGES_FOLDER', PHOTO_ARCHIVE_FOLDER.'images');
define('PHOTO_ARCHIVE_MID_FOLDER', PHOTO_ARCHIVE_FOLDER.'mid');
define('PHOTO_ARCHIVE_THUMB_FOLDER', PHOTO_ARCHIVE_FOLDER.'thumbs');

define('THUMB_X', '192');
define('THUMB_Y', '108');

define('MID_X', '1920');
define('MID_Y', '1080');

class photo_archive
{
    public function __construct()
    {
        spl_autoload_register(function($class) {
            $class = str_replace(__NAMESPACE__, '', $class);
            $class = ltrim($class, '\\');
            $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $class = strtolower($class);
            require_once $class.'.php';
        });

        echo $this->init();
    }

    public function init()
    {
        $c = new \F13Dev\PhotoArchive\Controllers\Control();
        return $c->photo_archive();
    }
}

new photo_archive();