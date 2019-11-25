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
    protected static $table_name = "Persons";
    public $username;
    public $name;
    public $surname;
    public $role;
    public $password;

    public static function getByName($name, $dbConnection)
    {
        try
        {
            $stmt = $dbConnection->prepare("select personID, username, name, surname, role, password from " . self::$table_name . " where username = ?");
            $stmt->execute([$name]);
            if($stmt->errorCode() != "00000")
                return null;
            $row = $stmt->fetch();
        }
        catch (\PDOException $e)
        {
            echo "exception";
            return null;
        }

        if($row == null)
        {
            return null;
        }

        $person = new Person($dbConnection);
        $person->id = $row['personID'];
        $person->username = $row['username'];
        $person->name = $row['name'];
        $person->surname = $row['surname'];
        $person->password = $row['password'];
        $person->role = $row['role'];
        return $person;
    }
    /**
     * Saves person to database as new one, if id is null, otherwise updates the one with same ID
     * @return bool true on success, false otherwise
     */
    public function save()
    {
        if(!$this->canSave())
        {
            return false;
        }

        if($this->id == null) //new person
        {
            /* //this control should be moved to upper level
            $controlPerson = new Person($this->connection);
            $controlPerson->username = $this->username;
            if(Person::findInDb($controlPerson) != null)
                return false;
            */
            $stmt = $this->connection->prepare("insert into " . self::$table_name . "(name, username, surname, role, password) values(?, ?, ?, ?, ?)");
        }
        else //updating person
        {
            //echo "updatuju osobu";
            $stmt = $this->connection->prepare("update " . self::$table_name . " set name = ?, username = ?, surname = ?, role = ?, password = ? where personID = ?");
        }

        try
        {
            $stmt->execute([$this->name, $this->username, $this->surname, $this->role, $this->password, $this->id]);
            if($this->id == null) //new person
                $this->id = $this->connection->lastInsertId();
            return true;
        }
        catch (\PDOException $e)
        {
            print_r($e->errorInfo);
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
            $this->username != null and
            $this->role != null and
            $this->password != null)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    public function delete()
    {
        if($this->id == null)
            return false;
        try
        {
            $stmt = $this->connection->prepare("delete from " . self::$table_name . " where personID = ?");
            $stmt->execute([$this->id]);
            return true;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    /**
     * @param $id integer - id of desired object
     * @param $dbConnection \PDO
     * @return Person found object from database, with filled fields (except other models) or null if not found
     */
    public static function getByID($id, $dbConnection)
    {
        try
        {
            $stmt = $dbConnection->prepare("select personID, username, name, surname, role, password from " . self::$table_name . " where personID = ?");
            $stmt->execute([$id]);
            if($stmt->errorCode() != "00000")
                return null;
            $row = $stmt->fetch();
        }
        catch (\PDOException $e)
        {
            echo "exception1";
            return null;
        }

        if($row == null)
        {
            return null;
        }

        $person = new Person($dbConnection);
        $person->id = $id;
        $person->username = $row['username'];
        $person->name = $row['name'];
        $person->surname = $row['surname'];
        $person->password = $row['password'];
        $person->role = $row['role'];
        return $person;
    }

    public function findInDb()
    {
        //TODO vyhledavat podle roli
        try
        {
            $stmt = $this->connection->prepare("SELECT * FROM " . self::$table_name . " WHERE role like ? and name like ? and surname like ? and username like ?");
            $stmt->execute([$this->AddPercentageChars($this->role),
                $this->AddPercentageChars($this->name),
                $this->AddPercentageChars($this->surname),
                $this->AddPercentageChars($this->username)]);
            if($stmt->errorCode() != "00000")
                return null;
        }
        catch (\PDOException $e)
        {
            return null;
        }
        $foundObjects = array();
        while($row = $stmt->fetch())
        {
            $foundPerson = new Person($this->connection);
            $foundPerson->name = $row['name'];
            $foundPerson->id = $row['personID'];
            $foundPerson->surname = $row['surname'];
            $foundPerson->password = $row['password'];
            $foundPerson->role = $row['role'];
            $foundPerson->username = $row['username'];
            array_push($foundObjects, $foundPerson);
        }
        return $foundObjects;
    }

    public function loadModels()
    {
        $this->modelsLoaded = true;
        return true;
    }
}