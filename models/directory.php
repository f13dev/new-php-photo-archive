<?php namespace F13Dev\PhotoArchive\Models;

class Directory
{
    public function get_items($dir)
    {
        //$m = new \F13Dev\PhotoArchive\Models\Database();

        $path = PHOTO_ARCHIVE_FOLDER.'images'.$dir;
        $folders = array();
        foreach(glob($path.'*', GLOB_ONLYDIR) as $folder) {
            //$folders[] = basename($dir);
            $folders[basename($folder)] = urlencode(str_replace(PHOTO_ARCHIVE_FOLDER.'images', '', $folder.'/'));
        }

        if ($dir != '/') {
            $parent = str_replace(PHOTO_ARCHIVE_FOLDER.'images/', '', $path);
            $parent = explode('/', $parent);
            array_pop($parent);
            array_pop($parent);
            $parent = '/'.implode('/', $parent).'/';
            if ($parent == '//') {
                $parent = '/';
            }
        } else {
            $parent = null;
        }

        $images = array();
        $count = 1;
        foreach (glob($path."*.{jpg,png,gif,JPG,PNG,GIF,mov,avi,mp4,MOV,AVI,MP4}", GLOB_BRACE) as $image) {     // ,mov,avi,MOV,AVI
            $image_url = str_replace(PHOTO_ARCHIVE_PATH.'/', PHOTO_ARCHIVE_URL, $image);
            $images[basename($image)]['image'] = $image_url;
            $ext = explode('.', $image_url);
            $ext = strtolower(end($ext));
            $images[basename($image)]['ext'] = $ext;
            switch ($ext) {
                case 'jpg': case 'png': case 'gif':
                    $images[basename($image)]['type'] = 'image';
                    break;
                case 'mov': case 'avi':
                    $images[basename($image)]['type'] = 'video_unsupported';
                    break;
                case 'mp4':
                    $images[basename($image)]['type'] = 'video';
                    break;
                default:
                    $images[basename($image)]['type'] = 'unknown';
            }
            if (file_exists(str_replace(PHOTO_ARCHIVE_IMAGES_FOLDER, PHOTO_ARCHIVE_THUMB_FOLDER, $image))) {
                $images[basename($image)]['thumb'] = str_replace(PHOTO_ARCHIVE_IMAGES_URL.'/', PHOTO_ARCHIVE_THUMB_URL, $image_url);
            } else 
            if (file_exists(str_replace(PHOTO_ARCHIVE_IMAGES_FOLDER, PHOTO_ARCHIVE_THUMB_FOLDER, $image).'.jpg')) {
                $images[basename($image)]['thumb'] = str_replace(PHOTO_ARCHIVE_IMAGES_URL.'/', PHOTO_ARCHIVE_THUMB_URL, $image_url).'.jpg';
            } else {
                $images[basename($image)]['thumb'] = $image_url;
            }
            if (file_exists(str_replace(PHOTO_ARCHIVE_IMAGES_FOLDER, PHOTO_ARCHIVE_MID_FOLDER, $image))) {
                $images[basename($image)]['mid'] = str_replace(PHOTO_ARCHIVE_IMAGES_URL.'/', PHOTO_ARCHIVE_MID_URL, $image_url);
            } else {
                $images[basename($image)]['mid'] = $image_url;
            }
            $images[basename($image)]['name'] = basename($image);
            $images[basename($image)]['number'] = $count;

            $ifd = @exif_read_data($image, 'IFD0');
            $exif = @exif_read_data($image, 'EXIF');

            $exif_data = '<strong>File: </strong>'.basename($image).'<br><br>';
            if (@array_key_exists('Make', $ifd)) {
                $exif_data .= '<strong>Make: </strong>:'.$ifd['Make'].'<br>';
            }
            if (@array_key_exists('Model', $ifd)) {
                $exif_data .= '<strong>Model: </strong>:'.$ifd['Model'].'<br>';
            }
            if (@array_key_exists('ExposureTime', $ifd)) {
                $exif_data .= '<strong>Exposure: </strong>:'.$ifd['ExposureTime'].'<br>';
            }
            if (@array_key_exists('ApertureFNumber', $ifd['COMPUTED'])) {
                $exif_data .= '<strong>Aperture: </strong>:'.$ifd['COMPUTED']['ApertureFNumber'].'<br>';
            }
            if (@array_key_exists('ISOSpeedRatings', $ifd)) {
                $exif_data .= '<strong>ISO: </strong>:'.$ifd['ISOSpeedRatings'].'<br>';
            }
            if (@array_key_exists('DateTime', $ifd)) {
                $date = \DateTime::createFromFormat('Y:m:d H:i:s', $ifd['DateTime']);
                $exif_data .= '<strong>Date: </strong>:'.$date->format('F, j Y - H:i').'<br>';
            }

            $images[basename($image)]['exif'] = $exif_data;

            $count++;
        }
        //$images = glob($path."*.{jpg,png,gif}", GLOB_BRACE);

        $return = new \stdClass();
        $return->folders = (object) $folders;
        $return->images = (object) $images;
        $return->parent = urlencode($parent);

        return $return;
    }
}