<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 26.11.2019
 * Time: 22:23
 */

namespace model;


class Work_on_tasks
{
    private static $table_name = "Work_on_tasks";
    public $total_time; //how much time of work have a worker really spent so far
    public $personID;
    public $taskID;
    protected $connection;


    /**
     * constructor.
     * @param $dbConnection \PDO
     */
    public function __construct($dbConnection)
    {
        $this->connection = $dbConnection;
    }

    /**
     * For saving new assignment ONLY!
     * @return bool true if success
     */
    public function saveNew()
    {
        if(!$this->canSave())
            return false;
        if($this->total_time == null)
            $this->total_time = 0;

        $stmt = $this->connection->prepare("insert into " . self::$table_name . "(taskID, personID, total_time) values(?, ?, ?)");
        try
        {
            $stmt->execute([$this->taskID, $this->personID, $this->total_time]);
            return true;
        }
        catch (\PDOException $e)
        {
            //print_r($e->errorInfo);
            return false;
        }
    }

    /**
     * Only for updating total_time (so far)
     * @return bool true if success
     */
    public function update()
    {
        if(!$this->canSave())
            return false;
        if($this->total_time == null)
            $this->total_time = 0;

        $stmt = $this->connection->prepare("update " . self::$table_name . "total_time = ? where personID = ? and taskID = ?");
        try
        {
            $stmt->execute([$this->total_time, $this->personID, $this->taskID]);
            return true;
        }
        catch (\PDOException $e)
        {
            //print_r($e->errorInfo);
            return false;
        }
    }

    /**
     * @param $personID int
     * @param $taskID int
     * @param $dbConnection \PDO
     * @return bool true on success
     */
    public static function delete($personID, $taskID, $dbConnection)
    {
        try
        {
            $stmt = $dbConnection->prepare("delete from " . self::$table_name . " where personID = ? and taskID = ?");
            $stmt->execute([$personID, $taskID]);
            return true;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    /**
     * @param $personID int
     * @param $dbConnection \PDO
     * @return array|null of all task which have the person assigned
     */
    static function getTasks($personID, $dbConnection)
    {
        try
        {
            $stmt = $dbConnection->prepare("select taskID from " . self::$table_name . " where personID = ?");
            $stmt->execute([$personID]);
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
            $foundTask = Task::getByID($row["taskID"], $dbConnection);
            array_push($foundObjects, $foundTask);
        }
        return $foundObjects;
    }

    /**
     * @param $taskID int
     * @param $dbConnection \PDO
     * @return array|null of all persons assigned to the task
     */
    static function getPersons($taskID, $dbConnection)
    {
        try
        {
            $stmt = $dbConnection->prepare("select personID from " . self::$table_name . " where taskID = ?");
            $stmt->execute([$taskID]);
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
            $foundTask = Task::getByID($row["personID"], $dbConnection);
            array_push($foundObjects, $foundTask);
        }
        return $foundObjects;
    }

    /**
     * @return bool false if missing critical field
     */
    protected function canSave()
    {
        if($this->personID == null || $this->taskID == null)
            return false;
        else
            return true;
    }
}