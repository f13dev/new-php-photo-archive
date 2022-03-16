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
        $dir = str_replace('>', DIRECTORY_SEPARATOR, $dir);
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

    public function edit_tags()
    {
        $file = filter_input($this->request_method, 'file');
        $folder = filter_input($this->request_method, 'folder');
        $number = (int) filter_input($this->request_method, 'number');
        $submit = (int) filter_input($this->request_method, 'submit');
        $tags = filter_input($this->request_method, 'tags', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $m = new \F13Dev\PhotoArchive\Models\Database();

        if ($submit) {
            $m->update_tags($folder, $file, $tags);

            $t = '';
            if (is_array($tags) && !empty($tags)) {
                foreach ($tags as $tag) {
                    $t .= '<span class="tag">'.htmlentities($tag, ENT_QUOTES).'</span>';
                }
            }
            return $t;
        }

        $db_tags = $m->select_tags_by_file($folder, $file);

        $tags = array();
        foreach ($db_tags as $tag) {
            if (is_array($tag) && array_key_exists('tag', $tag)) {
                $tags[] = $tag['tag'];
            }
        }

        $v = new \F13Dev\PhotoArchive\Views\Photo_archive(array(
            'tags' => $tags,
            'file' => $file,
            'folder' => $folder,
            'submit' => $submit,
            'number' => $number,
        ));

        return $v->edit_tags();
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

    public function suggest_tag()
    {
        $text = filter_input($this->request_method, 'text');

        $m = new \F13Dev\PhotoArchive\Models\Database();

        $tags = $m->select_tag_search($text);

        $v = '';
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $v .= '<div>'.htmlentities($tag['tag'], ENT_QUOTES).'</div>';
            }
        }

        return $v;

        print('<pre>'.print_r($tags, true).'</pre>');

    }

    public function search()
    {
        $terms = filter_input($this->request_method, 'tags', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $term = implode(',', $terms);
        
        $m = new \F13Dev\PhotoArchive\Models\Database();

        $results = $m->search($term);

        $items = new \stdClass();
        $items->images = $results;

        $v = new \F13Dev\PhotoArchive\Views\Photo_archive(array(
            'items' => $items,
            'dir' => 'Search',
            'container' => false,
        ));

        return $v->display();
    }
} 