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
    public static $table_name = "Products";
    public $name;
    public $parent_product;
    public $manager;
    public $description;

    //Funkce vraci pouze name a id, tzn nekompletni model!!!
    public static function getByName($name, $dbConnection)
    {
        try
        {
            $stmt = $dbConnection->prepare(
                "select productID, product_name, description, parent_product, manager from " . self::$table_name . " where product_name = ?");
            $stmt->execute([$name]);
            if($stmt->errorCode() != "00000")
                return null;
            $row = $stmt->fetch();
        }
        catch (\PDOException $e)
        {
            echo "exception v product getbyname";
            print_r($e->errorInfo);
            print_r($e->getMessage());
            return null;
        }

        if($row == null)
        {
            return null;
        }

        $product = new Product($dbConnection);
        $product->id = $row["productID"];
        $product->name = $row["product_name"];
        $product->description = $row["description"];
        $product->manager = new Person($dbConnection);
        $product->manager->id = $row["manager"];
        $product->parent_product = new Product($dbConnection);
        $product->parent_product->id = $row["parent_product"];
    }


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
                    $stmt = $this->connection->prepare(
                        "insert into " . self::$table_name . "(product_name, description, parent_product, manager) values(?, ?, ?, ?)");
                    $stmt->execute([$this->name, $this->description == null ? "" : $this->description, $this->parent_product->id, $this->manager->id]);
                }
                else
                {

                    $stmt = $this->connection->prepare(
                        "insert into " . self::$table_name . "(product_name, description, manager) values(?, ?, ?)");
                    $stmt->execute([$this->name, $this->description == null ? "" : $this->description, $this->manager->id]);
                }
                $this->id = $this->connection->lastInsertId();
            }
            else //updating
            {
                if ($this->parent_product == null and $this->modelsLoaded)
                {
                    if($this->manager == null)
                        echo "manager je null";

                    $stmt = $this->connection->prepare(
                        "update " . self::$table_name . " set product_name = ?, description = ?, manager = ?, parent_product = null where productID = ?");
                    $stmt->execute([$this->name, $this->description == null ? "" : $this->description, $this->manager->id, $this->id]);

                }
                else if ($this->parent_product == null and $this->manager == null and !$this->modelsLoaded)
                {
                    $stmt = $this->connection->prepare(
                        "update " . self::$table_name . " set product_name = ?, description = ? where productID = ?");
                    $stmt->execute([$this->name, $this->description == null ? "" : $this->description, $this->id]);
                }
                else if ($this->parent_product == null and $this->manager != null and !$this->modelsLoaded)
                {
                    $stmt = $this->connection->prepare(
                        "update " . self::$table_name . " set product_name = ?, description = ?, manager = ? where productID = ?");
                    $stmt->execute([$this->name, $this->description == null ? "" : $this->description, $this->manager->id, $this->id]);
                }
                else //parent_product != null and $this->manager == null and !$this->modelsLoaded
                {
                    $stmt = $this->connection->prepare(
                        "update " . self::$table_name . " set product_name = ?, description = ?, parent_product = ? where productID = ?");
                    $stmt->execute([$this->name, $this->description == null ? "" : $this->description,  $this->parent_product->id, $this->id]);
                }
            }
            return true;
        }
        catch (\PDOException $e)
        {
            print_r($e->errorInfo);
            return false;
        }
    }

    protected function canSave()
    {
        if( $this->name != null && $this->manager != null && $this->description != null)
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
            $stmt = $dbConnection->prepare("select productID, product_name, description from " . self::$table_name . " where productID = ?");
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
        $product->description = $row["description"];
        return $product;
    }

    /**
     * @param $dbConnection \PDO
     * @return array|Product|null
     */
    public static function getAll($dbConnection)
    {
        try
        {
            $stmt = $dbConnection->prepare("select productID, product_name, description from " . self::$table_name);
            $stmt->execute([]);
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
            $product = new Product($dbConnection);
            $product->id = $row['productID'];
            $product->name = $row['product_name'];
            $product->description = $row["description"];
            array_push($foundObjects, $product);
        }
        return $foundObjects;
    }

    public function findInDb()
    {
        try
        {
            if($this->parent_product == null)
            {
                $stmt = $this->connection->prepare(
                    "SELECT * FROM " . self::$table_name . " WHERE product_name like ?  and description like ? and parent_product is ? and manager like ?");
                $stmt->execute([$this->AddPercentageChars($this->name),
                    $this->AddPercentageChars($this->description == null ? "" : $this->description),
                    null,
                    $this->AddPercentageChars($this->manager == null ? "" : $this->manager->id)]);
            }
            else if($this->parent_product->id == "any")
            {
                $stmt = $this->connection->prepare(
                    "SELECT * FROM " . self::$table_name . " WHERE product_name like ?  and description like ?  and manager like ?");
                $stmt->execute([$this->AddPercentageChars($this->name),
                    $this->AddPercentageChars($this->description == null ? "" : $this->description),
                    $this->AddPercentageChars($this->manager == null ? "" : $this->manager->id)]);
            }
            else
            {
                $stmt = $this->connection->prepare(
                    "SELECT * FROM " . self::$table_name . " WHERE product_name like ? and description like ? and parent_product like ? and manager like ?");
                $stmt->execute([$this->AddPercentageChars($this->name),
                    $this->AddPercentageChars($this->description == null ? "" : $this->description),
                    $this->AddPercentageChars($this->parent_product == null ? "" : $this->parent_product->id),
                    $this->AddPercentageChars($this->manager == null ? "" : $this->manager->id)]);
            }
            if($stmt->errorCode() != "00000")
            {
                return null;
            }
        }
        catch (\PDOException $e)
        {
            //print_r($e->errorInfo);
            return null;
        }
        $foundObjects = array();
        while($row = $stmt->fetch())
        {
            $foundProduct = new Product($this->connection);
            $foundProduct->id = $row['productID'];
            $foundProduct->name = $row['product_name'];
            $foundProduct->description = $row["description"];
            array_push($foundObjects, $foundProduct);
        }
        return $foundObjects;
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