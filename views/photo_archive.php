<?php namespace F13Dev\PhotoArchive\Views;

class Photo_archive
{
    public $label_close;
    public $label_download;
    public $label_info;
    public $label_next;
    public $label_photo_archive;
    public $label_previous;

    public function __construct($params = array())
    {
        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }

        $this->label_close = 'Close';
        $this->label_download = 'Download';
        $this->label_info = 'Info';
        $this->label_next = 'Next';
        $this->label_photo_archive = 'Photo archive';
        $this->label_previous = 'Previous';
    }

    public function _container($content)
    {
        $v = '<!DOCTYPE html>';
        $v .= '<html>';
            $v .= '<head>';
                $v .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
                $v .= '<link rel="shortcut icon" href="'.PHOTO_ARCHIVE_URL.'image/favicon.ico">';
                $v .= '<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>';
                $v .= '<script src="'.PHOTO_ARCHIVE_URL.'js/photo_archive.js"></script>';
                $v .= '<link rel="stylesheet" href="'.PHOTO_ARCHIVE_URL.'css/photo_archive.css">';
                $v .= '<title>'.$this->label_photo_archive.'</title>';
            $v .= '</head>';
            $v .= '<body>';
                $v .= '<header>';
                    $v .= '<a href="'.PHOTO_ARCHIVE_URL.'"><h1>Photo archive</h1></a>';
                    $v .= '<span id="viewing">Viewing: /</span>';
                    $v .= '<span id="file_count">0</span>';
                    $v .= '<a href="'.PHOTO_ARCHIVE_URL.'/?ajax=1&do=resync_thumbs" id="sync" class="resync" data-target="#container" data-href="'.PHOTO_ARCHIVE_AJAX.'do=resync_thumbs">Re-sync gallery</a>';
                $v .= '</header>';

                $v .= '<div id="container">';
                    $v .= $content;
                $v .= '</div>';

                $v .= '<footer>';

                $v .= '</footer>';

                $v .= '<div id="lightbox">';
                    $v .= '<img id="lightbox-next" class="ajax-image" src="'.PHOTO_ARCHIVE_URL.'image/arrow-right-circle.svg" title="'.$this->label_next.'">';
                    $v .= '<img id="lightbox-prev" class="ajax-image" src="'.PHOTO_ARCHIVE_URL.'image/arrow-left-circle.svg" title="'.$this->label_previous.'">';
                    $v .= '<img id="lightbox-close" src="'.PHOTO_ARCHIVE_URL.'image/x-circle.svg" title="'.$this->label_close.'">';
                    $v .= '<a id="lightbox-download-link" download><img id="lightbox-download" src="'.PHOTO_ARCHIVE_URL.'image/download.svg" title="'.$this->label_download.'"></a>';
                    $v .= '<img id="lightbox-info" src="'.PHOTO_ARCHIVE_URL.'image/info.svg" title="'.$this->label_info.'">';
                    $v .= '<div id="lightbox-container">';
                        $v .= '<div id="lightbox-content">';
                            $v .= '<img src="'.PHOTO_ARCHIVE_URL.'image/robyn.jpg" id="lightbox-image">';
                        $v .= '</div>';
                    $v .= '</div>';
                    //$v .= '<div id="lightbox-caption"><span id="showing"></span></b><br><span class="tag">Robyn</span><span class="tag">Baby</span><br><br>DSC03014.JPG<br>03-Feb-2018<br>SONY DSLR-A200<br>1/2000sec<br>f/2.8<br>ISO800<br>10MP</div>';
                    $v .= '<div id="lightbox-caption"></div>';
                $v .= '</div>';
                $v .= '<div id="lightbox-loading">';
                    $v .= '<img id="lightbox-loading-image" src="'.PHOTO_ARCHIVE_URL.'image/loading.gif">';
                $v .= '</div>';
            $v .= '</body>';
        $v .= '</html>';

        return $v;
    }

    public function display()
    {
        $v = '';
        $v .= '<div id="folders_container">';
            if (!empty($this->items->parent)) {
                $v .= '<a href="?dir='.$this->items->parent.'" data-folder="'.$this->items->parent.'" class="ajax-link" data-target="#container" data-href="'.PHOTO_ARCHIVE_AJAX.'do=load_folder&folder='.$this->items->parent.'" title="View parent folder">';
                    $v .= '<div class="item">';
                        $v .= '<div class="folder folder-up"></div>';
                        $v .= '<div class="label">Parent folder</div>';
                    $v .= '</div>';
                $v .= '</a>';
            }

            foreach ($this->items->folders as $folder => $dir) {
                $v .= '<a href="?dir='.$dir.'" data-folder="'.$dir.'" class="ajax-link" data-target="#container" data-href="'.PHOTO_ARCHIVE_AJAX.'do=load_folder&folder='.$dir.'" title="View '.$folder.'">';
                    $v .= '<div class="item">';
                        $v .= '<div class="folder"></div>';
                        $v .= '<div class="label">'.$folder.'</div>';
                    $v .= '</div>';
                $v .= '</a>';
            }
        $v .= '</div>';

        /*
        $v .= '<div class="item">';
            $v .= '<div class="folder folder-prev"></div>';
            $v .= '<div class="label">Previous page</div>';
        $v .= '</div>';
        $v .= '<div class="item">';
            $v .= '<div class="folder folder-next"></div>';
            $v .= '<div class="label">Next page</div>';
        $v .= '</div>';
        $v .= '<div class="item">';
            $v .= '<div class="folder folder-download"></div>';
            $v .= '<div class="label">Download folder</div>';
        $v .= '</div>';
        */
        $v .= '<div class="images_container">';
            $total = count((array)$this->items->images);
            foreach ($this->items->images as $image) {
                $v .= '<a id="image-'.$image['number'].'" data-number="'.$image['number'].'" data-total="'.$total.'" class="ajax-image" href="'.$image['mid'].'" title="View '.$image['name'].'" data-ajax="popup" data-gallery="'.$this->dir.'" data-exif="<p><b>'.$image['number'].' of '.$total.'</b></p>'.$image['exif'].'">';
                    $v .= '<div class="item">';
                        $v .= '<div class="image"><img class="gallery-image" src="'.$image['thumb'].'" alt="View '.$image['name'].'" /></div>';
                        $v .= '<div class="label">'.$image['name'].'</div>';
                    $v .= '</div>';
                $v .= '</a>';
            }
        $v .= '</div>';

        echo '<script>document.getElementById("file_count").innerHTML = '.$total.';</script>';
        return (($this->container) ? $this->_container($v) : $v);
    }
}