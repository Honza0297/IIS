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
        $statement = "insert into " . self::$table_name . "(ticketID, comment_text, date_posted, author) values(\"$this->ticket->id\", \"$this->text\", \"$this->datePosted\", \"$this->author->id\");";
        try
        {
            $this->connection->exec($statement);
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
        return $this->runSql("delete from " . self::$table_name . " where commentID = '$this->id'");
    }

    public static function getByID($id, $dbConnection)
    {
        //fixme case not existing id, wrong connection... -> try catch?
        $stmt = $dbConnection->query("select commentID, date_posted, comment_text from " . self::$table_name . " where commentID = '$id'");
        $row = $stmt->fetch();
        $comment = new Comment($dbConnection);
        $comment->id = $id;
        $comment->text = $row['text'];
        $comment->datePosted = $row['date_posted'];
        return $comment;
    }

    public function findInDb($object)
    {
        // TODO: Implement findInDb() method.
    }

    public function loadModels()
    {

    }
}