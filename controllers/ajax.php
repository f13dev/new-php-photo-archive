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
            case 'edit_description': return $this->edit_description(); break;
            case 'edit_tags': return $this->edit_tags(); break;
            case 'suggest_tag'; return $this->suggest_tag(); break;
            case 'search'; return $this->search(); break;
        }
    }

    public function load_folder() { $c = new Control(); return $c->load_folder(); die; }
    public function resync_thumbs() { $c = new Resync(); return $c->resync_thumbs(); die; }
    public function edit_description() { $c = new Control(); return $c->edit_description(); die; }
    public function edit_tags() { $c = new Control(); return $c->edit_tags(); die; }
    public function suggest_tag() { $c = new Control(); return $c->suggest_tag(); die; }
    public function search() { $c = new Control(); return $c->search(); die; }
}