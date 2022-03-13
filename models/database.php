<?php namespace F13Dev\PhotoArchive\Models;

use Exception;
use PDOException;

class Database 
{
    private $dbc;

    public function __construct()
    {
        if (PHOTO_ARCHIVE_USE_DB) {
            try {
                $this->dbc = new \PDO('mysql:host='.PHOTO_ARCHIVE_DB_HOST.';dbname='.PHOTO_ARCHIVE_DB_DATABASE, PHOTO_ARCHIVE_DB_USER, PHOTO_ARCHIVE_DB_PASSWORD);
            } catch (Exception $e) {
                echo "<span class='database-error'>Database connection failed: ".$e->getMessage()."</span>";
                die();
            } catch(PDOException $e) {
                die ($e);
            }
        }
    }

    public function select_folder_data($folder)
    {
        if (PHOTO_ARCHIVE_USE_DB) {
            $sql = 'SELECT id, folder_name, file_name, description
                FROM files
                WHERE folder_name = :folder';
            $sth = $this->dbc->prepare($sql);
            $sth->execute(array('folder' => $folder));
            $resp = $sth->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resp as $key => $image) {
                $resp[$key]['tags'] = $this->select_tags($image['id']);
            }

            return $resp;
        }

        return array();
    }

    public function select_tags($file_id) 
    {
        if (PHOTO_ARCHIVE_USE_DB) {
            $sql = "SELECT db.file_id, db.tag_id, t.tag
                    FROM file_tag db
                    LEFT JOIN tags AS t ON (t.id = db.tag_id)
                    WHERE db.file_id = :file_id";
            $sth = $this->dbc->prepare($sql);
            $sth->execute(array('file_id' => $file_id));
            return $sth->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function select_tag_search($text)
    {
        if (PHOTO_ARCHIVE_USE_DB) {
            $sql = "SELECT db.tag
                    FROM tags db
                    WHERE db.tag LIKE :text
                    LIMIT 5;";
            $sth = $this->dbc->prepare($sql);
            $sth->execute(array('text' => '%'.$text.'%'));
            return $sth->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function select_tags_by_file($folder, $file)
    {
        if (PHOTO_ARCHIVE_USE_DB) {
            $sql = "SELECT db.file_id, db.tag_id, t.tag
                    FROM file_tag db
                    LEFT JOIN tags AS t ON (t.id = db.tag_id)
                    LEFT JOIN files AS f ON (f.id = db.file_id)
                    WHERE f.folder_name = :folder AND f.file_name = :file;";
            $sth = $this->dbc->prepare($sql);
            $sth->execute(array('folder' => $folder, 'file' => $file));
            return $sth->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function insert_file($folder, $file)
    {
        if (PHOTO_ARCHIVE_USE_DB) {
            $sql = "SELECT db.id
                    FROM files db
                    WHERE db.file_name = :file_name AND db.folder_name = :folder_name";
            $sth = $this->dbc->prepare($sql);
            $sth->execute(array('file_name' => $file, 'folder_name' => $folder));
            $resp = $sth->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($resp)) { 
                $sql = "INSERT INTO `files`
                            (`file_name`, `folder_name`)
                        VALUES
                            (?, ?);";
                $sth = $this->dbc->prepare($sql);
                return $sth->execute(array($file, $folder));
            } else {
                return true;
            }
        }
        return false;
    }

    public function insert_tag($folder, $file, $tag)
    {
        if (PHOTO_ARCHIVE_USE_DB) {
            // Ensure the record exists
            $this->insert_file($folder, $file);
        }
    }

    public function insert_description($folder, $file, $description)
    {
        if (PHOTO_ARCHIVE_USE_DB) {
            // Ensure the record exists
            $this->insert_file($folder, $file);
            $sql = "UPDATE files
                    SET description = :description
                    WHERE file_name = :file_name AND folder_name = :folder_name;";
            $sth = $this->dbc->prepare($sql);
            return ($sth->execute(array('description' => $description, 'file_name' => $file, 'folder_name' => $folder)));
        }
    }

    public function select_description($folder, $file)
    {
        if (PHOTO_ARCHIVE_USE_DB) {
            $sql = "SELECT db.description
                    FROM files db
                    WHERE db.folder_name = :folder_name AND db.file_name = :file_name;";
            $sth = $this->dbc->prepare($sql);
            $sth->execute(array('folder_name' => $folder, 'file_name' => $file));
            return $sth->fetch(\PDO::FETCH_ASSOC);
        }
    }
}