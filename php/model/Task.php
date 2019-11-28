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
    protected static $table_name = "Tasks";
    public $title;
    public $state;
    public $description;
    public $deadline;
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
                if($this->deadline == null)
                    $this->deadline = 0;

                $stmt = $this->connection->prepare("insert into " . self::$table_name . "(description, title, state, ticketID, deadline) values(?, ?, ?, ?, ?)");
                $stmt->execute([$this->description, $this->title, $this->state, $this->ticket->id, $this->deadline]);
                $this->id = $this->connection->lastInsertId();
            }
            else //updating
            {
                if ($this->modelsLoaded || $this->ticket != null)
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set title = ?, state = ?, ticketID = ?, description = ?, deadline = ?  where taskID = ?");
                    $stmt->execute([$this->title, $this->state, $this->ticket->id, $this->description, $this->deadline, $this->id]);
                }
                else //!$this->modelsLoaded and ticket == null
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set title = ?, state = ?, description = ?, deadline = ? where taskID = ?");
                    $stmt->execute([$this->title, $this->state, $this->description, $this->deadline, $this->id]);
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
        if( $this->title != null and
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
        $task->title = $row['title'];
        $task->state = $row['state'];
        $task->description = $row['description'];
        $task->deadline = $row['deadline'];
        return $task;
    }

    public function findInDb()
    {
        try
        {
            $stmt = $this->connection->prepare(
                "SELECT * FROM " . self::$table_name . " WHERE title like ? and state like ? and ticketID like ? and description like ? and deadline like ?");
            $stmt->execute([$this->AddPercentageChars($this->title),
                $this->AddPercentageChars($this->state),
                $this->AddPercentageChars($this->ticket == null ? "" : $this->ticket->id),
                $this->AddPercentageChars($this->description),
                $this->AddPercentageChars($this->deadline)]);
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
            $foundTask->deadline = $row['deadline'];
            $foundTask->state = $row['state'];
            $foundTask->title = $row['title'];
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