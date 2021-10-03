<?php namespace F13Dev\PhotoArchive\Controllers;

class Resync
{
    public $request_method;

    public function __construct()
    {
        $this->request_method = ($_SERVER['REQUEST_METHOD'] === 'POST') ? INPUT_POST : INPUT_GET;
    }

    public function _create_folders_iterator()
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(PHOTO_ARCHIVE_IMAGES_FOLDER), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $path => $object) {
            if (substr($path, -1) != '.') {
                //echo $path.'<br>';

                $thumb = str_replace(PHOTO_ARCHIVE_IMAGES_FOLDER, PHOTO_ARCHIVE_THUMB_FOLDER, $path);
                $mid = str_replace(PHOTO_ARCHIVE_IMAGES_FOLDER, PHOTO_ARCHIVE_MID_FOLDER, $path);

                if (is_dir($path)) {
                    echo str_pad('.', 4096);
                    if (!file_exists($thumb)) {
                        if (mkdir($thumb, 0777)) {
                            echo str_pad('<br>'.date('Y-m-d H:i:s').' - Created folder: '.str_replace(PHOTO_ARCHIVE_PATH, '', $thumb),4096);
                            if (touch($thumb.DIRECTORY_SEPARATOR.'index.php')) {
                                echo str_pad('<br>'.date('Y-m-d H:i:s').' - Created NULL index: '.str_replace(PHOTO_ARCHIVE_PATH, '', $thumb).DIRECTORY_SEPARATOR.'index.php',4096);
                            } else {
                                echo str_pad('<br>'.date('Y-m-d H:i:s').' - Error creating NULL index: '.str_replace(PHOTO_ARCHIVE_PATH, '', $thumb).DIRECTORY_SEPARATOR.'index.php',4096);
                            }
                        } else {
                            echo str_pad('<br>'.date('Y-m-d H:i:s').' - Error creating folder: '.str_replace(PHOTO_ARCHIVE_PATH, '', $thumb),4096);
                        }
                    }

                    if (!file_exists($mid)) {
                        if (mkdir($mid, 0777)) {
                            echo str_pad('<br>'.date('Y-m-d H:i:s').' - Created folder: '.str_replace(PHOTO_ARCHIVE_PATH, '', $mid),4096);
                            if (touch($mid.DIRECTORY_SEPARATOR.'index.php')) {
                                echo str_pad('<br>'.date('Y-m-d H:i:s').' - Created NULL index: '.str_replace(PHOTO_ARCHIVE_PATH, '', $mid).DIRECTORY_SEPARATOR.'index.php',4096);
                            } else {
                                echo str_pad('<br>'.date('Y-m-d H:i:s').' - Error creating NULL index: '.str_replace(PHOTO_ARCHIVE_PATH, '', $mid).DIRECTORY_SEPARATOR.'index.php',4096);
                            }
                        } else {
                            echo str_pad('<br>'.date('Y-m-d H:i:s').' - Error creating folder: '.str_replace(PHOTO_ARCHIVE_PATH, '', $mid),4096);
                        }
                    }
                    echo str_pad('<script>window.scrollTo(0,document.body.scrollHeight);</script>', 4096);
                }
                set_time_limit(20);
            }
        }
    }

    public function _create_image($source, $destination, $max_x = 1920, $max_y = 1080, $size = 'Thumb')
    {
        if (file_exists($destination)) {
            return false;
        }

        $extensions = array('jpg', 'jpeg', 'gif', 'png');
        $extension = explode('.', $source);
        $extension = end($extension);

        if (in_array(strtolower($extension), $extensions)) {
            list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source);
            switch ($source_image_type) {
                case IMAGETYPE_GIF:
                    $source_gd_image = imagecreatefromgif($source);
                    break;
                case IMAGETYPE_JPEG:
                    $source_gd_image = imagecreatefromjpeg($source);
                    break;
                case IMAGETYPE_PNG:
                    $source_gd_image = imagecreatefrompng($source);
                    break;
                default:
                    $source_gd_image = false;
            }

            if ($source_gd_image == false) {
                return false;
            }

            $source_aspect_ratio = $source_image_width / $source_image_height;
            $thumbnail_aspect_ratio = $max_x / $max_y;
            if ($source_image_width <= $max_x && $source_image_height <= $max_y) {
                $thumbnail_image_width = $source_image_width;
                $thumbnail_image_height = $source_image_height;
            } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
                $thumbnail_image_width = (int) ($max_y * $source_aspect_ratio);
                $thumbnail_image_height = $max_y;
            } else {
                $thumbnail_image_width = $max_x;
                $thumbnail_image_height = (int) ($max_x / $source_aspect_ratio);
            }
            $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
            imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0,0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
            imagejpeg($thumbnail_gd_image, $destination, 90);
            imagedestroy($source_gd_image);
            imagedestroy($thumbnail_gd_image);

            echo str_pad('<br>'.date('Y-m-d H:i:s').' - Created file: '.str_replace(PHOTO_ARCHIVE_PATH, '', $destination),4096);

            return true;
        }
    }

    public function _create_thumbs_iterator()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(PHOTO_ARCHIVE_IMAGES_FOLDER), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $path => $object) {
            echo str_pad('.', 4096);
            if (substr($path, -1) != '.') {

                $thumb = str_replace(PHOTO_ARCHIVE_IMAGES_FOLDER, PHOTO_ARCHIVE_THUMB_FOLDER, $path);
                $mid = str_replace(PHOTO_ARCHIVE_IMAGES_FOLDER, PHOTO_ARCHIVE_MID_FOLDER, $path);

                $this->_create_image($path, $thumb, THUMB_X, THUMB_Y, 'Thumb');
                $this->_create_image($path, $mid, MID_X, MID_Y, 'Mid');
            }
            echo str_pad('<script>window.scrollTo(0,document.body.scrollHeight);</script>', 4096);
            set_time_limit(20);
        }
    }

    public function _unused_thumbs_iterator()
    {

    }

    public function resync_thumbs()
    {
        echo str_pad('<style>body { background: #000; color: #00aa00; } </style>', 4096);
        echo str_pad(date('Y-m-d H:i:s').' - Starting sync', 4096);
        echo str_pad('<br>'.date('Y-m-d H:i:s').' - Checking folders', 4096);
        $this->_create_folders_iterator();
        echo str_pad('<br>'.date('Y-m-d H:i:s').' - Checking images', 4096);
        $this->_create_thumbs_iterator();
        //$this->_unused_thumbs_iterator();
    }
}