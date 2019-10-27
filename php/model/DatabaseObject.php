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
    protected $table_name;
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
    public abstract function getByID();
    public abstract function findInDb($object);
    public abstract function loadModels();
}