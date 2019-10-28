<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 21:25
 */

namespace model;
include_once "DatabaseObject.php";


class Ticket extends DatabaseObject
{
    protected static $table_name = "tickets";
    public $title;
    public $info;
    public $state;
    public $date_posted;
    public $author;
    public $product;

    /**
     * Saves databaseObject to database as new one, if id is null, otherwise updates the one with same ID
     * @return bool true on success, false otherwise
     */
    public function save()
    {
        if(!$this->canSave())
            return false;

        if($this->info == null)
            $this->info = "";

        try
        {
            if ($this->id == null) //new object
            {
                if ($this->author == null || $this->product == null) //required for new ticket
                    return false;
                $stmt = $this->connection->prepare("insert into " . self::$table_name . "(title, info, state, date_posted, author, product) values(?, ?, ?, ?, ?, ?)");
                $stmt->execute([$this->title, $this->info, $this->state, $this->date_posted, $this->author->id, $this->product->id]);
                $this->id = $this->connection->lastInsertId();
            }
            else //updating
            {
                // TODO: check this
                if ($this->modelsLoaded)
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set title = ?, info = ?, state = ?, date_posted = ?, author = ?, product = ? where ticketID = ?");
                    $stmt->execute([$this->title, $this->info, $this->state, $this->date_posted, $this->author->id, $this->product->id, $this->id]);
                }
                else //!$this->modelsLoaded
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set title = ?, info = ?, state = ?, date_posted = ? where ticketID = ?");
                    $stmt->execute([$this->title, $this->info, $this->state, $this->date_posted, $this->id]);
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
            $this->state != null and
            $this->date_posted != null)
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
            $stmt = $this->connection->prepare("delete from " . self::$table_name . " where ticketID = ?");
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
            $stmt = $dbConnection->prepare("select ticketID, title, info, date_posted, state from " . self::$table_name . " where ticketID = ?");
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
        $ticket = new Ticket($dbConnection);
        $ticket->id = $id;
        $ticket->title = $row['title'];
        $ticket->info = $row['info'];
        $ticket->date_posted = $row['date_posted'];
        $ticket->state = $row['state'];
        return $ticket;
    }

    public function findInDb($object)
    {
        // TODO: Implement findInDb() method.
    }

    public function loadModels()
    {
        try
        {
            $stmt = $this->connection->prepare("select product, author from " . self::$table_name . " where ticketID = ?");
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

        $this->product = Product::getByID($row['product'], $this->connection);
        $this->author = Person::getByID($row['author'], $this->connection);

        if($this->product == null || $this->author == null)
            return false;

        $this->modelsLoaded = true;
        return true;
    }
}