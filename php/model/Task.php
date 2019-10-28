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
        if(!$this->canSave())
            return false;

        try
        {
            if ($this->id == null) //new object
            {
                if ($this->ticket == null) //required for new task
                    return false;
                $stmt = $this->connection->prepare("insert into " . self::$table_name . "(task_type, state, ticketID) values(?, ?, ?)");
                $stmt->execute([$this->type, $this->state, $this->ticket->id]);
                $this->id = $this->connection->lastInsertId();
            }
            else //updating
            {
                // TODO: check this (if you change ticket when models are not loaded, you cannot save it)
                if ($this->modelsLoaded)
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set task_type = ?, state = ?, ticketID = ? where taskID = ?");
                    $stmt->execute([$this->type, $this->state, $this->ticket->id, $this->id]);
                }
                else //!$this->modelsLoaded
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set task_type = ?, state = ? where taskID = ?");
                    $stmt->execute([$this->type, $this->state, $this->id]);
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
            $stmt = $dbConnection->prepare("select taskID, task_type, state from " . self::$table_name . " where taskID = ?");
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
        return $task;
    }

    public function findInDb($object)
    {
        // TODO: Implement findInDb() method.
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