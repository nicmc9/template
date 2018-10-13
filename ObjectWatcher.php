<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12.10.2018
 * Time: 10:07
 */

/*
 * Дубликаты объектов это не только риск но и непроизводительные издержки
 * Некоторые популярные объекты могут загружаться по три четыре раза в процессе
 * и все эти обращения могут быть совершенно излешними
 */
require_once ("Model.php");

class ObjectWatcher {
    private $all = array();
    private $dirty = array();
    private $new = array();
    private $delete = array(); // в нашем примере не используется
    private static $instance = null;

    private function __construct()
    {
    }

    static function instance(){
        if(is_null(self::$instance)){
            self::$instance = new ObjectWatcher();
        }
        return self::$instance;
    }

    function globalKey(\woo\domain\DomainObject $object){
        $key = get_class($object).".".$object->getId();
        return $key;
    }

    static function add(\woo\domain\DomainObject $object){
        $inst = self::instance();
        $inst->all[$inst->globalKey($object)] = $object;
    }

    static function exists($classname,$id){
        $inst = self::instance();
        $key = "{$classname}.{$id}";
        if(isset($inst->all[$key])){
            return $inst->all[$key];
        }
        return null;
    }

    static  function  addDelete(\woo\domain\DomainObject $object){
        $self = self::instance();
        $self->delete[$self->globalKey($object)]=$object;
    }

    static  function  addDirty(\woo\domain\DomainObject $object){
        $inst = self::instance();
        if(!in_array($object,$inst->new,true)) {
            $inst->dirty[$inst->globalKey($object)] = $object;
        }
    }

    static function addNew(\woo\domain\DomainObject $object){
        $inst = self::instance();
        // У нас еще нет идентификатора id
        $inst->new[] = $object;
    }

    static function addClean(\woo\domain\DomainObject $object){
        $self = self::instance();
        unset($self->delete[$self->globalKey($object)]);
        unset($self->dirty[$self->globalKey($object)]);
        $self->new = array_filter($self->new, function ($a) use($object){return !($a===$object);});
    }

    function performOperations(){
        foreach ($this->dirty as $key=>$obj){
            $obj->finder()->update($obj);
        }
        foreach ($this->new as $key=>$obj){
            $obj->finder()->insert($obj);
            print "inserting {$obj->getName()}\n";
        }
        $this->dirty = array();
        $this->new = array();
    }

}