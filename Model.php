<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10.10.2018
 * Time: 16:30
 */

namespace woo\domain;





interface VenueCollection extends \Iterator
{
    function add(DomainObject $venue);
}

interface SpaceCollection extends \Iterator
{
    function add(DomainObject $space);
}

interface EventCollection extends \Iterator
{
    function add(DomainObject $event);
}


abstract class DomainObject {
    private $id;
    function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    static function getCollection($type){
        return array(); // заглушка
    }

    function collection(){
        return self::getCollection(get_class($this));
    }
}

class Venue extends DomainObject {

    private $name;
    private $spaces;

    function __construct($id = null,$name = null)
    {
        parent::__construct($id);

        $this->name = $name;
        $this->spaces = self::getCollection("\\woo\\domain\\Space");

    }

    function setSpaces(SpaceCollection $space){
        $this->spaces = $space;
    }

    function getSpaces(){
        return $this->spaces;
    }

    function addSpace(Space $space){
        $this->spaces->add($space);
        $space->setVenue($this);
    }

    /**
     * @param null $name
     */
    public function setName($name_s)
    {
        $this->name = $name_s;
      //  $this->markDirty();
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

}

class Space extends DomainObject {

    private $name;
    private $venue;
    private $events;

    function __construct($id = null,$name = null,$venue=null)
    {
        $this->name = $name;
        $this->venue = $venue;
        $this->events = self::getCollection("\\woo\\domain\\Event");
        parent::__construct($id);
    }

    function setEvents(EventCollection $events){
        $this->events = $events;
    }

    function getEvents(){
        return $this->events;
    }


    /**
     * @param mixed $venue
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;
    }

    /**
     * @return null
     */
    public function getVenue()
    {
        return $this->venue;
    }
    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;

    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

}

class Event extends DomainObject {

    private $name;
    private $space;
    private $start;
    private $duration;

    function __construct($id = null,$name,$space,$start,$duration)
    {
        $this->name = $name;
        $this->space = $space;
        $this->start = $start;
        $this->duration = $duration;
        parent::__construct($id);
    }


    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;

    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

}