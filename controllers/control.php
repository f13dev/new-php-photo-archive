<?php namespace F13Dev\PhotoArchive\Controllers;

class Control
{
    public $request_method;

    public function __construct()
    {
        $this->request_method = ($_SERVER['REQUEST_METHOD'] === 'POST') ? INPUT_POST : INPUT_GET;
    }

    public function photo_archive()
    {
        $dir = filter_input($this->request_method, 'dir');
        $ajax = filter_input($this->request_method, 'ajax');

        if (!empty($ajax)) {
            $a = new Ajax();
            return $a->do_ajax($ajax);
            die;
        }

        $dir = urldecode($dir);
        if (empty($dir)) {
            $dir = '/';
        }
        //echo '<script>alert("'.$dir.'")</script>';

        $m = new \F13Dev\PhotoArchive\Models\Directory();
        $items = $m->get_items($dir);

        $v = new \F13Dev\PhotoArchive\Views\Photo_archive(array(
            'items' => $items,
            'dir' => $dir,
            'container' => true,
        ));

        return $v->display();
    }

    public function load_folder()
    {
        $folder = filter_input($this->request_method, 'folder');

        $folder = urldecode($folder);
        if (empty($folder)) {
            $folder = '/';
        }

        $m = new \F13Dev\PhotoArchive\Models\Directory();
        $items = $m->get_items($folder);

        $v = new \F13Dev\PhotoArchive\Views\Photo_archive(array(
            'items' => $items,
            'dir' => $folder,
            'container' => false,
        ));

        return $v->display();
    }

    public function edit_description()
    {
        $file = filter_input($this->request_method, 'file');
        $folder = filter_input($this->request_method, 'folder');
        $description = filter_input($this->request_method, 'description');
        $number = (int) filter_input($this->request_method, 'number');
        $submit = (int) filter_input($this->request_method, 'submit');

        $m = new \F13Dev\PhotoArchive\Models\Database();

        if ($submit) {
            if ($m->insert_description($folder, $file, $description)) {
                return htmlentities($description, ENT_QUOTES);
            }
        }

        $i = $m->select_description($folder, $file);

        $description = (is_array($i) && array_key_exists('description', $i)) ? $i['description'] : '';
        
        $v = new \F13Dev\PhotoArchive\Views\Photo_archive(array(
            'description' => $description,
            'file' => $file,
            'folder' => $folder,
            'submit' => $submit,
            'number' => $number,
        ));

        return $v->edit_description();
    }
}