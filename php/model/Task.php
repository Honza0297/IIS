<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 22:00
 */

namespace model;
include_once "DatabaseObject.php";


class Task extends DatabaseObject
{
    protected static $table_name = "tasks";
    public $type;
    public $state;
    public $ticket;

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
        if($this->id == null)
            return false;
        try
        {
            $stmt = $this->connection->prepare("delete from " . self::$table_name . " where taskID = ?");
            $stmt->execute([$this->id]);
            return true;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    public static function getByID($id, $dbConnection)
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