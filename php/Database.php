<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 16:23
 */

class Database
{
     
     private $socket = '/var/run/mysql/mysql.sock';
     private $host = 'localhost';
     private $dbName = 'xberan43';
     private $username = 'xberan43';
     private $password = 'i7ezingi';

    public $connection;


    public function getConnection()
    {
        $this->connection = null;
        try
        {
            $this->connection = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->connection->exec("set names utf8");
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->connection;
    }
}
?>
