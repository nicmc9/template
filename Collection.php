<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 11.10.2018
 * Time: 12:53
 */

namespace woo\mapper;

// Решает много задач
/*
 * Ненужная инициальизация если возвращать большой массив объектов
 *
 */


abstract class Collection implements \Iterator {

    protected $mapper;
    protected $total = 0;
    protected $raw = array();

    private $result;
    private $pointer = 0;
    private $objects = array();

    function __construct(array $raw=null, Mapper $mapper=null)
    {
        if(!is_null($raw)&&!is_null($mapper)){
            $this->raw =$raw;
            $this->total = count($raw);
        }
        $this->mapper = $mapper;
    }

    function add(\woo\domain\DomainObject $object){

        $class = $this->targetClass();
        if(!($object instanceof $class)){
            throw new \Exception("Это коллесция {$class}");
        }
        $this->notifyAccess();
        $this->objects[$this->total] = $object;
        $this->total++;
    }

    abstract function targetClass();
    protected function notifyAccess(){}

    private function getRow($num){
        $this->notifyAccess();
        if($num>=$this->total||$num<0){
            return null;
        }
        if(isset($this->objects[$num])){
            return $this->objects[$num];
        }
        if(isset($this->raw[$num])){
            $this->objects[$num] = $this->mapper->createObject($this->raw[$num]);
            return $this->objects[$num];
        }
    }

    public function rewind(){
        $this->pointer = 0;
    }

    public function current()
    {
        return $this->getRow($this->pointer);
    }
    public function key()
    {
        return $this->pointer;
    }
    public function next()
    {
       $row = $this->getRow($this->pointer);
       if($row){
           $this->pointer++;
       }
       return $row;
    }
    public function valid()
    {
        return (! is_null($this->current()));
    }

}

class VenueCollection extends Collection implements \woo\domain\VenueCollection{

    function targetClass()
    {
        return "\woo\domain\Venue";
    }

}


class SpaceCollection extends Collection implements \woo\domain\SpaceCollection{

    function targetClass()
    {
        return "\woo\domain\Space";
    }
}

class EventCollection extends Collection implements \woo\domain\EventCollection{

    function targetClass()
    {
        return "\woo\domain\Event";
    }
}