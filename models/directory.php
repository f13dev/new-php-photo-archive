<?php namespace F13Dev\PhotoArchive\Models;

class Directory
{
    public static function image_array($image_url, $count, $data = null)
    {
        $image = array();
        $image['image'] = $image_url;
        $ext = explode('.', $image_url);
        $ext = strtolower(end($ext));
        $image['ext'] = $ext;
        switch ($ext) {
            case 'jpg': case 'png': case 'gif':
                $image['type'] = 'image';
                break;
            case 'mov': case 'avi':
                $image['type'] = 'video_unsupported';
                break;
            case 'mp4':
                $image['type'] = 'video';
                break;
            default:
                $image['type'] = 'unknown';
        }
        $image['name'] = basename($image_url);
        $image['folder'] = str_replace(basename($image_url), '', str_replace(PHOTO_ARCHIVE_IMAGES_URL, '', $image_url));
        $image['number'] = $count;

        if (file_exists(PHOTO_ARCHIVE_MID_FOLDER.'/'.ltrim($image['folder'], '/').$image['name'])) {
            $image['mid'] = PHOTO_ARCHIVE_MID_URL.ltrim($image['folder'], '/').$image['name'];
        } else {
            $image['mid'] = $image_url;
        }
        if (file_exists(PHOTO_ARCHIVE_THUMB_FOLDER.'/'.ltrim($image['folder'], '/').$image['name'])) {
            $image['thumb'] = PHOTO_ARCHIVE_THUMB_URL.ltrim($image['folder'], '/').$image['name'];
        } else 
        if (file_exists(PHOTO_ARCHIVE_THUMB_FOLDER.'/'.ltrim($image['folder'], '/').$image['name']).'.jpg') {
            $image['thumb'] = PHOTO_ARCHIVE_THUMB_URL.ltrim($image['folder'], '/').$image['name'].'.jpg';
        } else {
            $image['thumb'] = $image['mid'];
        }

        $ifd = @exif_read_data(str_replace(PHOTO_ARCHIVE_URL, PHOTO_ARCHIVE_PATH.'/', $image_url), 'IFD0');

        $exif_data = '<strong>File: </strong>'.$image['name'].'<br>';
        $exif_data .= '<strong>Folder: </strong>'.$image['folder'].'<br><br>';
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
        if (@array_key_exists('Copyright', $ifd) && !empty(trim($ifd['Copyright']))) {
            $exif_data .= '<strong>Copyright: </strong>'.$ifd['Copyright'].'<br>';
        }

        $image['exif'] = $exif_data;

        // Database driven items 
        $db_file = false;
        if (!empty($data)) {
            foreach ($data as $data_item) {
                if (
                    array_key_exists('file_name', $data_item) &&
                    $data_item['file_name'] == basename($image_url)
                ) {
                    $db_file = $data_item;
                    break;
                }

            }
        } else {
            $m = new \F13Dev\PhotoArchive\Models\Database();
            $db_file = $m->select_file_data($image['folder'], $image['name']);
        }

        $image['db_id'] = false;
        if ($db_file) {
            $image['db_id'] = $db_file['id'];
        }

        $image['description'] = '';
        if ($db_file && array_key_exists('description', $db_file)) {
            $image['description'] = $db_file['description'];
        }

        $image['tags'] = json_encode(array());
        if ($db_file) {
            $image['tags'] = json_encode($db_file['tags']);
        }

        return $image;
    }














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

        // Select SQL data if it exists
        $m = new \F13Dev\PhotoArchive\Models\Database();
        $data = $m->select_folder_data($dir);
                
        $images = array();
        $count = 1;
        foreach (glob($path."*.{jpg,png,gif,JPG,PNG,GIF,mov,avi,mp4,MOV,AVI,MP4}", GLOB_BRACE) as $image) {     // ,mov,avi,MOV,AVI
            $image_url = str_replace(PHOTO_ARCHIVE_PATH.'/', PHOTO_ARCHIVE_URL, $image);
            $images[basename($image)] = \F13Dev\PhotoArchive\Models\Directory::image_array($image_url, $count, $data);
            /*
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
            $images[basename($image)]['folder'] = str_replace(basename($image_url), '', str_replace(PHOTO_ARCHIVE_IMAGES_URL, '', $image_url));
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

            // Database driven items 
            $db_file = false;
            if (!empty($data)) {
                foreach ($data as $data_item) {
                    if (
                        array_key_exists('file_name', $data_item) &&
                        $data_item['file_name'] == basename($image)
                    ) {
                        $db_file = $data_item;
                        break;
                    }

                }
            }

            $images[basename($image)]['db_id'] = false;
            if ($db_file) {
                $images[basename($image)]['db_id'] = $db_file['id'];
            }

            $images[basename($image)]['description'] = '';
            if ($db_file && array_key_exists('description', $db_file)) {
                $images[basename($image)]['description'] = $db_file['description'];
            }

            $images[basename($image)]['tags'] = json_encode(array());
            if ($db_file) {
                $images[basename($image)]['tags'] = json_encode($db_file['tags']);
            }
            */
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