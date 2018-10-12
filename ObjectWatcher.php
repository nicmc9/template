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

}