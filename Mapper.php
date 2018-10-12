<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10.10.2018
 * Time: 17:11
 */

namespace woo\mapper;
require_once ("Model.php");
require_once ("ObjectWatcher.php");
use woo\domain\DomainObject;
use woo\domain\Venue;
use woo\domain\Space;
use woo\domain\Event;

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

    private function getFromMap($id){
        return \ObjectWatcher::exists($this->targetClass(),$id);
    }

    private function addToMap(DomainObject $object){
         \ObjectWatcher::add($object);

    }



    function find($id){
       $old = $this->getFromMap($id);
       if(!is_null($old)){ return $old;}


      $this->selectStmt()->execute(array($id));
      $array = $this->selectStmt()->fetch();
      $this->selectStmt()->closeCursor();
            if(!is_array($array)) {return null;}
            if(!isset($array['id'])) {return null;}

      $object = $this->createObject($array);

            return $object;
    }

    function findAll(){
        $this->selectAllStmt()->execute(array());
           return $this->getCollection($this->selectAllStmt()->fetchAll(PDO::FETCH_ASSOC));
    }

    function createObject($array){
        $old = $this->getFromMap($array['id']);
        if(!is_null($old)){ return $old;}

        $obj = $this->doCreateObject($array);
        $this->addToMap($obj);
        $obj->markClean(); //  Если поменять конструктор Domain и создавать массив по признаку
        return $obj;
    }

    function insert(\woo\domain\DomainObject $obj){

        $this->doInsert($obj);
        $this->addToMap($obj);
        return;
    }

    abstract protected function targetClass();
    abstract function update(\woo\domain\DomainObject $object);
    abstract function getCollection(array $raw);
    protected abstract function doCreateObject(array $array);
    protected abstract function doInsert(\woo\domain\DomainObject $object);
    protected abstract function selectStmt();
    protected abstract function selectAllStmt();
}

class VenueMapper extends Mapper {

    function __construct()
    {

        parent::__construct();
        $this->selectStmt = self::$PDO->prepare("SELECT * FROM venue WHERE id = ?");
        $this->updateStmt = self::$PDO->prepare("UPDATE venue SET name=?, id=? WHERE id=?");
        $this->insertStmt = self::$PDO->prepare("INSERT into venue (name) values(?)");
        $this->selectAllStmt = self::$PDO->prepare("SELECT * FROM venue");

    }

    function getCollection(array $raw){
        return new VenueCollection($raw,$this);
    }
    protected function targetClass()
    {
        return \woo\domain\Venue::class;
    }

    protected function doCreateObject(array $array)
    {
       print $array['id'];
       $obj = new Venue($array['id']);
       $obj->setName($array['name']);
       $space_mapper = new SpaceMapper();
       $space_collection = $space_mapper->findByVenue($array['id']);
       $obj->setSpaces($space_collection);
       return $obj;
    }

    protected function doInsert(\woo\domain\DomainObject $object)
    {
        $values = array($object->getName());
        $this->insertStmt->execute($values);
        $id=self::$PDO->lastInsertId();
        $object->setId($id);
        return;
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

    function selectAllStmt()
    {
        return $this->selectAllStmt;
    }
}


class SpaceMapper extends Mapper
{

    function __construct()
    {

        parent::__construct();
        $this->selectStmt = self::$PDO->prepare("SELECT * FROM space WHERE id = ?");
        $this->updateStmt = self::$PDO->prepare("UPDATE space SET name=?, id=? WHERE id=?");
        $this->insertStmt = self::$PDO->prepare("INSERT into space (name,venue) values(?,?)");
        $this->selectAllStmt = self::$PDO->prepare("SELECT * FROM space");
        $this->findByVenueStmt = self::$PDO->prepare("SELECT * FROM space where venue=?");

    }

    function getCollection(array $raw){
        return new SpaceCollection($raw,$this);
    }

    protected function targetClass()
    {
        return \woo\domain\Space::class;
    }

    public function findByVenue($vid){

        $this->findByVenueStmt->execute(array($vid));
        return new SpaceCollection($this->findByVenueStmt->fetchAll(),$this);

    }


    protected function doCreateObject(array $array)
    {

        $obj = new Space($array['id']);

        $obj->setName($array['name']);
        $ven_mapper = new VenueMapper();
        $venue = $ven_mapper->find($array['venue']);
        $obj->setVenue($venue);
        // Переносим findBySpaceId в getEvents класса Space

    //    $event_mapper = new EventMapper();
   //     $event_collection = $event_mapper->findBySpaceId($array['id']);
    //    $obj->setEvents($event_collection);

        return $obj;
    }

    protected function doInsert(\woo\domain\DomainObject $object)
    {
        $values = [$object->getName(),$object->getVenueId()];
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
    function selectAllStmt()
    {
        return $this->selectAllStmt;
    }

}

class EventMapper extends Mapper
{

    function __construct()
    {

        parent::__construct();
        $this->selectStmt = self::$PDO->prepare("SELECT * FROM event WHERE id = ?");
        $this->updateStmt = self::$PDO->prepare("UPDATE event SET name=?, id=? WHERE id=?");
        $this->insertStmt = self::$PDO->prepare("INSERT into event (name) values(?)");
        $this->selectAllStmt = self::$PDO->prepare("SELECT * FROM event");
        $this->SelectBySpaceStmt =self::$PDO->prepare("SELECT * FROM event WHERE space = ?");
    }

    function getCollection(array $raw){
        return new EventCollection($raw,$this);
    }

    protected function targetClass()
    {
        return \woo\domain\Event::class;
    }

    function findBySpaceId($s_id){
            return new DeferredEventCollection($this,$this->SelectBySpaceStmt,array($s_id));
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
    function selectAllStmt()
    {
        return $this->selectAllStmt;
    }
}