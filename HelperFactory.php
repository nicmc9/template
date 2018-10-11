<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 11.10.2018
 * Time: 15:34
 */
namespace woo\domain;

require_once ("Collection.php");

use woo\mapper\VenueCollection;
use woo\mapper\SpaceCollection;
use woo\mapper\EventCollection;
use woo\mapper\VenueMapper;
use woo\mapper\SpaceMapper;
use woo\mapper\EventMapper;

//$collection = \woo\domain\HelperFactory::getCollection(dom\Venue::class);
class HelperFactory
{

    static function getCollection($class){

        if($class == "woo\domain\Venue") {
            return new VenueCollection();
        }
        if($class == "woo\domain\Space") {
            return new SpaceCollection();
        }
        if($class == "woo\domain\Event") {
            return new EventCollection();
        }
    }

    static function getFinder($class){

        if($class == "woo\domain\Venue") {
            return new VenueMapper();
        }
        if($class == "woo\domain\Space") {
            return new SpaceMapper();
        }
        if($class == "woo\domain\Event") {
            return new EventMapper();
        }
    }
}