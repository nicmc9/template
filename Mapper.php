<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10.10.2018
 * Time: 17:11
 */

namespace woo\mapper;

abstract class Mapper{
    protected static $PDO;

    function __construct()
    {
        if(!isset(self::$PDO)) {
            $dsn = \woo\base\ApplicationRegistry::getDSN();

            if (is_null($dsn)) {
                throw new \Exception("DSN не определен");
            }

            self::$DB = new \PDO($dsn);
            self::$DB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
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

