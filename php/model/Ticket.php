<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 21:25
 */

namespace model;
include_once "DatabaseObject.php";


class Ticket extends DatabaseObject
{
    protected $table_name = "tickets";
    public $title;
    public $info;
    public $state;
    public $date_posted;
    public $author;
    public $product;

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