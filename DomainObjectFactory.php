<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12.10.2018
 * Time: 14:18
 */

namespace woo\mapper;

use woo\domain\Venue;

abstract class DomainObjectFactory{
    abstract function createObject(array $array);
}

class VenueObjectFactory extends DomainObjectFactory{


    // Здесь также реализуеться кэширование через ObjectWatcher()
    // Не допускайте клонов объектов приложения это делает систему неуправляемой
    function createObject(array $array)
    {
       $obj = new Venue($array['id']);
       $obj->setName($array['name']);
       return $obj;
    }
}