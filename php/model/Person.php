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
    protected static $table_name = "persons";
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
            $statement = "insert into " . self::$table_name . "(name, surname, role, password) values(\"$this->name\", \"$this->surname\", \"$this->role\", \"$this->password\");";
        }
        else //updating person
            $statement = "update " . self::$table_name . " set name = '$this->name', surname = '$this->surname', role = '$this->role', password = '$this->password' where personID = '$this->id';";

        try
        {
            $this->connection->exec($statement);
            if($this->id == null) //new person
                $this->id = $this->connection->lastInsertId();
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
        if($this->id == null)
            return false;
        return $this->runSql("delete from " . self::$table_name . " where personID = '$this->id'");
    }

    /**
     * @param $id integer - id of desired object
     * @param $dbConnection \PDO
     * @return Person found object from database, with filled fields (except other models) or null if not found
     */
    public static function getByID($id, $dbConnection)
    {
        //fixme case not existing id, wrong connection... -> try catch?
        try
        {
            $stmt = $dbConnection->query("select personID, name, surname, role, password from " . self::$table_name . " where personID = '$id'");
            $row = $stmt->fetch();
        }
        catch (\PDOException $e)
        {
            echo "zachynce";
            return null;
        }
        /*
        if($row == null)
            echo "stmt je null";
        else
            echo "stmt neni null";
        $person = new Person($dbConnection);
        $person->id = $id;
        $person->name = $row['name'];
        $person->surname = $row['surname'];
        $person->password = $row['password'];
        $person->role = $row['role'];
        return $person;
        */
    }

    public function findInDb($object)
    {
        // TODO: Implement findInDb() method.
    }

    public function loadModels()
    {
        $this->modelsLoaded = true;
        return true;
    }
}