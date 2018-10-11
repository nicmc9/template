<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10.10.2018
 * Time: 17:11
 */

namespace woo\mapper;
require_once ("Model.php");
use woo\domain\Venue;

abstract class Mapper{
    protected static $PDO;

    function __construct()
    {
        if(!isset(self::$PDO)) {
            //$dsn = \woo\base\ApplicationRegistry::getDSN();
            $dsn = 'mysql:dbname=ven;host=localhost';
            $username = 'root';
            $password = '1234';
//            if (is_null($dsn)) {
//                throw new \Error("DSN не определен");
//            }

            self::$PDO = new \PDO($dsn,$username,$password);
            self::$PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        }
    }

    function find($id){

      $this->selectStmt()->execute(array($id));
      $array = $this->selectStmt()->fetch();
      $this->selectStmt()->closeCursor();
            if(!is_array($array)) {return null;}
            if(!isset($array['id'])) {return null;}

      $object = $this->createObject($array);

            return $object;
    }

    function createObject($array){
        $obj = $this->doCreateObject($array);
        return $obj;
    }

    function insert(\woo\domain\DomainObject $obj){
        $this->doInsert($obj);
    }

    abstract function update(\woo\domain\DomainObject $object);
    protected abstract function doCreateObject(array $array);
    protected abstract function doInsert(\woo\domain\DomainObject $object);
    protected abstract function selectStmt();
}

class VenueMapper extends Mapper {

    function __construct()
    {

        parent::__construct();
        $this->selectStmt = self::$PDO->prepare("SELECT * FROM venue WHERE id = ?");
        $this->updateStmt = self::$PDO->prepare("UPDATE venue SET name=?, id=? WHERE id=?");
        $this->insertStmt = self::$PDO->prepare("INSERT into venue (name) values(?)");

    }

    function getCollection(array $raw){
        return new SpaceCollection($raw,$this);
    }

    protected function doCreateObject(array $array)
    {
        print $array['id'];
       $obj = new Venue($array['id']);
       $obj->setName($array['name']);
       return $obj;
    }

    protected function doInsert(\woo\domain\DomainObject $object)
    {
        $values = array($object->getName());
        $this->insertStmt->execute($values);
        $id=self::$PDO->lastInsertId();
        $object->setId($id);
    }

    function update(\woo\domain\DomainObject $object)
    {
       $values = array($object->getName(), $object->getId(), $object->getId());
       $this->updateStmt->execute($values);
    }

    function selectStmt()
    {
       return $this->selectStmt;
    }

}

