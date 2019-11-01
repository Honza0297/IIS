<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 22:01
 */

namespace model;
include_once "DatabaseObject.php";


class Comment extends DatabaseObject
{
    protected static $table_name = "comments";
    public $ticket;
    public $text;
    public $datePosted;
    public $author;

    /**
     * Saves databaseObject to database as new one
     * @return bool true on success, false otherwise
     */
    public function save()
    {
        if(!$this->canSave())
            return false;
        try
        {
            $stmt = $this->connection->prepare("insert into " . self::$table_name . "(ticketID, comment_text, date_posted, author) values(?, ?, ?, ?)");
            $stmt->execute([$this->id, $this->text, $this->datePosted, $this->author->id]);
            $this->id = $this->connection->lastInsertId();
            return true;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    /**
     * Checks if all required fields are filled - editing is forbidden for comments
     * @return bool
     */
    protected function canSave()
    {
        if( $this->text != null and
            $this->datePosted != null and
            $this->author != null and
            $this->ticket != null and
            $this->id == null)
            return true;
        else
            return false;
    }

    public function delete()
    {
        if($this->id == null)
            return false;
        try
        {
            $stmt = $this->connection->prepare("delete from " . self::$table_name . " where commentID = ?");
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
            $stmt = $dbConnection->prepare("select commentID, date_posted, comment_text from " . self::$table_name . " where commentID = ?");
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
        $comment = new Comment($dbConnection);
        $comment->id = $id;
        $comment->text = $row['text'];
        $comment->datePosted = $row['date_posted'];
        return $comment;
    }

    public function findInDb()
    {
        // TODO: Implement findInDb() method.
    }

    /**
     * Loads other models to the fields of this instance
     * @return bool true on success, false otherwise
     */
    public function loadModels()
    {
        try
        {
            $stmt = $this->connection->prepare("select ticketID, author from " . self::$table_name . " where commentID = ?");
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
        $this->author = Person::getByID($row['author'], $this->connection);

        if($this->ticket == null || $this->author == null)
            return false;

        $this->modelsLoaded = true;
        return true;
    }
}