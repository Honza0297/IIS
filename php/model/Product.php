<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 18:31
 */

namespace model;
include_once "DatabaseObject.php";


class Product extends DatabaseObject
{
    protected static $table_name = "products";
    public $name;
    public $parent_product;
    public $manager;

    public function save()
    {
        if(!$this->canSave())
            return false;

        try {
            if ($this->id == null) //new object
            {
                if ($this->manager == null) //manager is required for new product
                    return false;
                if ($this->parent_product != null) //parent product is optional
                {
                    $stmt = $this->connection->prepare("insert into " . self::$table_name . "(product_name, parent_product, manager) values(?, ?, ?)");
                    $stmt->execute([$this->name, $this->parent_product->id, $this->manager->id]);
                }
                else
                {
                    $stmt = $this->connection->prepare("insert into " . self::$table_name . "(product_name, manager) values(?, ?)");
                    $stmt->execute([$this->name, $this->manager->id]);
                }
                $this->id = $this->connection->lastInsertId();
            }
            else //updating
            {
                // TODO: check this
                if ($this->parent_product == null and $this->modelsLoaded)
                {
                    if($this->manager == null)
                        echo "manager je null";
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set product_name = ?, manager = ?, parent_product = null where productID = ?");
                    $stmt->execute([$this->name, $this->manager->id, $this->id]);
                }
                else if ($this->parent_product == null and $this->manager == null and !$this->modelsLoaded)
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set product_name = ? where productID = ?");
                    $stmt->execute([$this->name, $this->id]);
                }
                else if ($this->parent_product == null and $this->manager != null and !$this->modelsLoaded)
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set product_name = ?, manager = ? where productID = ?");
                    $stmt->execute([$this->name, $this->manager->id, $this->id]);
                }
                else //parent_product != null and $this->manager == null and !$this->modelsLoaded
                {
                    $stmt = $this->connection->prepare("update " . self::$table_name . " set product_name = ?, parent_product = ? where productID = ?");
                    $stmt->execute([$this->name, $this->parent_product->id, $this->id]);
                }
            }
            return true;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    protected function canSave()
    {
        if( $this->name != null)
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
            $stmt = $this->connection->prepare("delete from " . self::$table_name . " where productID = ?");
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
            $stmt = $dbConnection->prepare("select productID, product_name from " . self::$table_name . " where productID = ?");
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
        $product = new Product($dbConnection);
        $product->id = $id;
        $product->name = $row['product_name'];
        return $product;
    }

    public function findInDb($object)
    {
        // TODO: Implement findInDb() method.
    }

    public function loadModels()
    {
        try
        {
            $stmt = $this->connection->prepare("select parent_product, manager from " . self::$table_name . " where productID = ?");
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

        $this->manager = Person::getByID($row['manager'], $this->connection);
        $this->parent_product = Product::getByID($row['parent_product'], $this->connection);
        if($this->manager == null)
            return false;

        $this->modelsLoaded = true;
        return true;
    }


}