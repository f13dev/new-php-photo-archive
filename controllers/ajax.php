<?php namespace F13Dev\PhotoArchive\Controllers;

class Ajax
{
    public $request_method;

    public function __construct()
    {
        $this->request_method = ($_SERVER['REQUEST_METHOD'] === 'POST') ? INPUT_POST : INPUT_GET;
    }

    public function do_ajax($method = null)
    {
        $do = filter_input($this->request_method, 'do');
        if (empty($do)) {
            return 'Ajax call missing "do" parameter.';
        }
        switch ($do) {
            case 'load_folder': return $this->load_folder(); break;
            case 'resync_thumbs': return $this->resync_thumbs(); break;
        }
    }

    public function load_folder() { $c = new Control(); return $c->load_folder(); die; }
    public function resync_thumbs() { $c = new Resync(); return $c->resync_thumbs(); die; }
}