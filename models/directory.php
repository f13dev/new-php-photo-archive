<?php namespace F13Dev\PhotoArchive\Models;

class Directory
{
    public function get_items($dir)
    {
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
        foreach (glob($path."*.{jpg,png,gif,JPG,PNG,GIF}", GLOB_BRACE) as $image) {
            $image_url = str_replace(PHOTO_ARCHIVE_PATH.'/', PHOTO_ARCHIVE_URL, $image);
            $images[basename($image)]['image'] = $image_url;
            if (file_exists(str_replace(PHOTO_ARCHIVE_IMAGES_FOLDER, PHOTO_ARCHIVE_THUMB_FOLDER, $image))) {
                $images[basename($image)]['thumb'] = str_replace(PHOTO_ARCHIVE_IMAGES_URL.'/', PHOTO_ARCHIVE_THUMB_URL, $image_url);
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

            $exif_data = '';
            if (@array_key_exists('Make', $ifd)) {
                $exif_data .= '<b>Make: </b>:'.$ifd['Make'].'<br>';
            }
            if (@array_key_exists('Model', $ifd)) {
                $exif_data .= '<b>Model: </b>:'.$ifd['Model'].'<br>';
            }
            if (@array_key_exists('ExposureTime', $ifd)) {
                $exif_data .= '<b>Exposure: </b>:'.$ifd['ExposureTime'].'<br>';
            }
            if (@array_key_exists('ApertureFNumber', $ifd['COMPUTED'])) {
                $exif_data .= '<b>Aperture: </b>:'.$ifd['COMPUTED']['ApertureFNumber'].'<br>';
            }
            if (@array_key_exists('ISOSpeedRatings', $ifd)) {
                $exif_data .= '<b>ISO: </b>:'.$ifd['ISOSpeedRatings'].'<br>';
            }
            if (@array_key_exists('DateTime', $ifd)) {
                $date = \DateTime::createFromFormat('Y:m:d H:i:s', $ifd['DateTime']);
                $exif_data .= '<b>Date: </b>:'.$date->format('F, j Y - H:i').'<br>';

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