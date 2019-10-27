<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 22:01
 */

namespace model;
include_once "DatabaseObject.php";


class Comment extends DatabaseObject
{
    protected $table_name = "comments";
    public $ticket;
    public $text;
    public $datePosted;
    public $author;

    /**
     * Saves databaseObject to database as new one, if id is null, otherwise updates the one with same ID
     * @return bool true on success, false otherwise
     */
    public function save()
    {
        // TODO: Implement save() method.
    }

    /**
     * Checks if all required fields are filled
     * @return bool
     */
    protected function canSave()
    {
        // TODO: Implement canSave() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function getByID()
    {
        // TODO: Implement getByID() method.
    }

    public function findInDb($object)
    {
        // TODO: Implement findInDb() method.
    }

    public function loadModels()
    {
        // TODO: Implement loadModels() method.
    }
}