<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 11.10.2018
 * Time: 13:04
 */

//phpinfo();
require_once ("Mapper.php");
require_once ("HelperFactory.php");
require_once ("Model.php");
use woo\domain\Venue;
use woo\domain\Space;
use woo\domain\HelperFactory;

use \woo\mapper\VenueMapper;
//
$mapper = new VenueMapper();


//print '<pre>';
//print_r($venue);
//print '</pre>';
//
//$venue2 = new \woo\domain\Venue();
//$venue2->setName("The Likey Lounge");
//$mapper->insert($venue2);
//$venue2 = $mapper->find(1);
//
//print '<pre>';
//print_r($venue2);
//print '</pre>';
//
//$venue->setName("The Bibble Beer");
//
//$mapper->update($venue);
//
//$venue = $mapper->find($venue2->getId());
//print '<pre>';
//print_r($venue);
//print '</pre>';

//print_r(Venue::class);
//
////$collection = HelperFactory::getCollection(Venue::class);
//$collection = Venue::getCollection();
//
//try {
//    $collection->add(new Venue(null, "Loud and Thumping"));
//    $collection->add(new Venue(null, "Eeezy"));
//    $collection->add(new Venue(null, "Duck and Badger"));
//}catch (Exception $exception){
//    print "Че-то не так {$exception}";
//
//}
//foreach ($collection as $value){
//    print '<pre>';
//    print_r($value);
//    print '</pre>';
//}
//

//$venue = new Venue();
//$mapper = $venue->finder();
//
//$venue->setName("The Likey Louge");
//
//$mapper->insert($venue);
//$venue = $mapper->find($venue->getId());
//print '<pre>';
//print_r($venue);
//print '</pre>';
//
//
//$venue->setName("The bibble Beer Likey Lounge");
//$mapper->update($venue);
//
//$venue=$mapper->find($venue->getId());
//print '<pre>';
//print_r($venue);
//print '</pre>';

$venue = new Venue(null,"The Grenn Trees");
$venue->addSpace(new Space(null,'The Space Upstairs'));
$venue->addSpace(new Space(null,'The Bar Stage'));

ObjectWatcher::instance()->performOperations();

//[Xdebug]
//zend_extension="C:\php-7.2.9-nts-Win32-VC15-x64\php_xdebug-2.6.1-7.2-vc15-nts-x86_64.dll"
//xdebug.remote_enable=1
//xdebug.remote_port="9000"

?>