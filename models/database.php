<?php namespace F13Dev\PhotoArchive\Models;

class Database 
{
    private $dbc;

    public function __construct()
    {
        /*
        $this->dbc = new \PDO(
            "mysql:host=".PHOTO_ARCHIVE_DB_HOST."
            ;dbname=".PHOTO_ARCHIVE_DB_DATABASE.",
            '".PHOTO_ARCHIVE_DB_USER."', '".PHOTO_ARCHIVE_DB_PASSWORD."'"
        );
        */
        $this->dbc = new \mysqli(
            PHOTO_ARCHIVE_DB_HOST,
            PHOTO_ARCHIVE_DB_USER,
            PHOTO_ARCHIVE_DB_PASSWORD,
            PHOTO_ARCHIVE_DB_DATABASE
        );

        if ($this->dbc->connect_error) {
            die("Connection failed: " . $dbc->connect_error);
        }
    }

    public function select_folder_data($folder)
    {

    }

    public function insert_tag($folder, $file, $tag)
    {

    }

    public function insert_description($folder, $file, $description)
    {

    }

    public function install()
    {

    }
}