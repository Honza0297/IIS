<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 22:00
 */

namespace model;
include_once "DatabaseObject.php";


class Attachment extends DatabaseObject
{
    protected static $table_name = "Attachments";
    public $ticket;
    public $filename;

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
                $stmt = $this->connection->prepare("insert into " . self::$table_name . "(tickedID, filename) values(?, ?)");
                $stmt->execute([$this->ticket->id, $this->filename]);
                $this->id = $this->connection->lastInsertId();
            }
            else //updating is not possible
            {
               return false;
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
        if( $this->ticket != null and
            $this->filename != null)
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
            $stmt = $this->connection->prepare("delete from " . self::$table_name . " where ID = ?");
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
            $stmt = $dbConnection->prepare("select ID, ticketID, filename from " . self::$table_name . " where ID = ?");
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
        $attachment = new Attachment($dbConnection);
        $attachment->filename = $row["filename"];
        return $attachment;
    }

    public function findInDb()
    {
       //TODO je to potřeba?
    }

    public function loadModels()
    {
        try
        {
            $stmt = $this->connection->prepare("select ticketID from " . self::$table_name . " where ID = ?");
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