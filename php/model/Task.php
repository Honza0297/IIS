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
    public $description;
    public $estimated_time;
    public $total_time;
    public $ticket;

    /**
     * Saves databaseObject to database as new one, if id is null, otherwise updates the one with same ID
     * @return bool true on success, false otherwise
     */
    public function save()
    {
        if(!$this->canSave())
            return false;

        try
        {
            if ($this->id == null) //new object
            {
                if ($this->ticket == null) //required for new task
                    return false;
                if($this->estimated_time == null)
                    $this->estimated_time = 0;
                if($this->total_time == null)
                    $this->total_time = 0;
                $stmt = $this->connection->prepare("insert into " . self::$table_name . "(description, task_type, state, ticketID, estimated_time, total_time) values(?, ?, ?, ?, ?, ?)");
                $stmt->execute([$this->description, $this->type, $this->state, $this->ticket->id, $this->estimated_time, $this->total_time]);
                $this->id = $this->connection->lastInsertId();
            }
            else //updating
            {
                if ($this->modelsLoaded || $this->ticket != null)
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set task_type = ?, state = ?, ticketID = ?, description = ?, estimated_time = ?, total_time = ?  where taskID = ?");
                    $stmt->execute([$this->type, $this->state, $this->ticket->id, $this->description, $this->estimated_time, $this->total_time, $this->id]);
                }
                else //!$this->modelsLoaded and ticket == null
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set task_type = ?, state = ?, description = ?, estimated_time = ?, total_time = ?  where taskID = ?");
                    $stmt->execute([$this->type, $this->state, $this->description, $this->estimated_time, $this->total_time, $this->id]);
                }
            }
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
        if( $this->type != null and
            $this->description != null and
            $this->state != null)
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
        try
        {
            $stmt = $dbConnection->prepare("select * from " . self::$table_name . " where taskID = ?");
            $stmt->execute([$id]);
            if($stmt->errorCode() != "00000")
                return null;
            $row = $stmt->fetch();
        }
        catch (\PDOException $e)
        {
            return null;
        }

        if($row == null)
            return null;
        $task = new Task($dbConnection);
        $task->id = $id;
        $task->type = $row['task_type'];
        $task->state = $row['state'];
        $task->description = $row['description'];
        $task->estimated_time = $row['estimated_time'];
        $task->total_time = $row['total_time'];
        return $task;
    }

    public function findInDb()
    {
        try
        {
            $stmt = $this->connection->prepare(
                "SELECT * FROM " . self::$table_name . " WHERE task_type like ? and state like ? and ticketID like ? and description like ? and estimated_time like ? and total_time like ? ");
            $stmt->execute([$this->AddPercentageChars($this->type),
                $this->AddPercentageChars($this->state),
                $this->AddPercentageChars($this->ticket == null ? "" : $this->ticket->id),
                $this->AddPercentageChars($this->description),
                $this->AddPercentageChars($this->estimated_time),
                $this->AddPercentageChars($this->total_time)]);
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
            $foundTask = new Task($this->connection);
            $foundTask->description = $row['description'];
            $foundTask->id = $row['taskID'];
            $foundTask->estimated_time = $row['estimated_time'];
            $foundTask->state = $row['state'];
            $foundTask->total_time = $row['total_time'];
            $foundTask->type = $row['task_type'];
            array_push($foundObjects, $foundTask);
        }
        return $foundObjects;
    }

    public function loadModels()
    {
        try
        {
            $stmt = $this->connection->prepare("select ticketID from " . self::$table_name . " where taskID = ?");
            $stmt->execute([$this->id]);
            if($stmt->errorCode() != "00000")
                return false;
            $row = $stmt->fetch();
        }
        catch (\PDOException $e)
        {
            return false;
        }

        if($row == null)
            return false;

        $this->ticket = Ticket::getByID($row['ticketID'], $this->connection);

        if($this->ticket == null)
            return false;

        $this->modelsLoaded = true;
        return true;
    }
}