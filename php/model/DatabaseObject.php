<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 18:33
 */

namespace model;


abstract class DatabaseObject
{
    protected $connection;
    protected static $table_name;
    protected $modelsLoaded = true; //flag for marking that models are loaded too.
    public $id;


    /**
     * constructor.
     * @param $dbConnection \PDO
     */
    public function __construct($dbConnection)
    {
        $this->connection = $dbConnection;
    }

    protected function runSql($statement)
    {
        try
        {
            $this->connection->execute($statement);
            return true;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    /**
     * Saves databaseObject to database as new one, if id is null, otherwise updates the one with same ID
     * @return bool true on success, false otherwise
     */
    public abstract function save();

    /**
     * Checks if all required fields are filled
     * @return bool
     */
    protected abstract function canSave();
    public abstract function delete();

    /**
     * @param $id integer - id of desired object
     * @param $dbConnection \PDO
     * @return DatabaseObject found object from database, with filled fields (except other models)
     */
    public static abstract function getByID($id, $dbConnection);
    public abstract function findInDb($object);
    public abstract function loadModels();
}