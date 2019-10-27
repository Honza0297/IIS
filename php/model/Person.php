<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 16:33
 */

namespace model;
include_once "DatabaseObject.php";


class Person extends DatabaseObject
{
    protected $table_name = "persons";
    public $name;
    public $surname;
    public $role;
    public $password;

    /**
     * Saves person to database as new one, if id is null, otherwise updates the one with same ID
     * @return bool true on success, false otherwise
     */
    public function save()
    {
        if(!$this->canSave())
            return false;

        if($this->id == null) //new person
        {
            $statement = "insert into $this->table_name(name, surname, role, password) values(\"$this->name\", \"$this->surname\", \"$this->role\", \"$this->password\");";
        }
        else //updating person
            $statement = "update $this->table_name set name = '$this->name', surname = '$this->surname', role = '$this->role', password = '$this->password' where personID = '$this->id';";

        try
        {
            $this->connection->exec($statement);
            return true;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    /**
     * Checks if all required fields are filled
     * @return bool
     */
    protected function canSave()
    {
        if( $this->name != null and
            $this->surname != null and
            $this->role != null and
            $this->password != null)
        {
            return true;
        }
        else
            return false;
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