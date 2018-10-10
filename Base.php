<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10.10.2018
 * Time: 14:56
 */

/*
 * Быстрый и эффективный механизм достижения целей системы без
 * потенциально дорогостоящих вложений в сложный проект
 */

namespace woo\process;

abstract class Base{
    static $DB;
    static $statements = [];

       function __construct()
       {
           $dsn = \woo\base\ApplicationRegistry::getDSN();
           if(is_null($dsn)){
               throw new \Exception("DSN not allow");
           }
           self::$DB = new \PDO($dsn);
           self::$DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
       }

       function prepareStatement($statement){
           if(isset(self::$statements[$statement])){
               return self::$statements[$statement];
           }
           $stmt_handle = self::$DB->prepare($statement);
           self::$statements[$statement] = $stmt_handle;
           return $stmt_handle;
       }

       public function doStatement($statement, array $values){
           $sth = $this->prepareStatement($statement);
           $sth->closeCursor();
           $db_result = $sth->execute($values);
           return $db_result;
       }

}

class VenueManager extends Base{
    static $add_venue = "INSERT INTO venue (name) VALUES (?)";
    static $add_space = "INSERT INTO space (name, venue) VALUES (?,?)";
    static $check_slot = "SELECT id,name FROM event WHERE space = ? AND (start+duration)>? AND start <?";
    static $add_event = "INSERT INTO event (name,space,start,duration) VALUES (?,?,?,?)";

    function addVenue($name, $space_array)
    {
        $venuedata = array();
        $venuedata['venue'] = array($name);
        $this->doStatement(self::$add_venue, $venuedata['venue']);
        $v_id = self::$DB->lastInsertId();
        $venuedata['space'] = array();
        foreach ($space_array as $space_name) {
            $values = array($space_name,$v_id);
            $this->doStatement(self::$add_space,$values);
            $s_id = self::$DB->lastInsertId();
            array_unshift($values,$s_id);
            $venuedata['spaces'][] = $values;
        }
        return $venuedata;
    }


    function bookEvent($space_id,$name,$time,$duration){
        $values = array($space_id,$time,($time+$duration));
        $stmt = $this->doStatement(self::$check_slot,$values);
                if($result = $stmt->fetch()){
                    throw  new \Exception("Already exist");
                }
                $this->doStatement(self::$add_event , array($name,$space_id,$time,$duration));
    }
}