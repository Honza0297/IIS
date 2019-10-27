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
    protected $table_name = "products";
    public $name;
    public $parent_product;
    public $manager;

    public function save()
    {
        if(!$this->canSave())
            return false;

        if($this->id == null) //new object
        {
            if($this->manager == null) //manager is required for new product
                return false;
            if($this->parent_product != null) //parent product is optional
                $statement = "insert into $this->table_name(product_name, parent_product, manager) values(\"$this->name\", \"$this->parent_product->id\", \"$this->manager->id\");";
            else
                $statement = "insert into $this->table_name(product_name, manager) values(\"$this->name\", \"$this->manager->id\");";
        }
        else //updating
        { //todo check this
            if($this->parent_product == null and $this->modelsLoaded)
                $statement = "update $this->table_name set product_name = '$this->name', manager = '$this->manager->id', parent_product = null where productID = '$this->id';";
            else if($this->parent_product == null and !$this->modelsLoaded)
                $statement = "update $this->table_name set product_name = '$this->name' where productID = '$this->id';";
            else //parent_product != null and !$this->modelsLoaded
                $statement = "update $this->table_name set product_name = '$this->name', parent_product = '$this->parent_product->id' where productID = '$this->id';";
        }

        try
        {
            $this->connection->exec($statement);
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
        // TODO: Implement delete() method.
    }

    public function getByID()
    {
        // TODO: Implement getByID() method.
    }

    public function findInDb($object)
    {
        // TODO: Implement findInDb() method.
    }

    public function loadModels()
    {
        // TODO: Implement loadModels() method.
    }
}