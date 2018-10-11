<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 11.10.2018
 * Time: 13:04
 */

//phpinfo();
require_once ("Mapper.php");

use \woo\mapper\VenueMapper;

$mapper = new VenueMapper();

$venue = $mapper->find(1);
print '<pre>';
print_r($venue);
print '</pre>';

$venue2 = new \woo\domain\Venue();
$venue2->setName("The Likey Lounge");
$mapper->insert($venue2);

$venue = $mapper->find($venue2->getId());
print '<pre>';
print_r($venue);
print '</pre>';

$venue->setName("The Bibble Beer");

$mapper->update($venue);

$venue = $mapper->find($venue2->getId());
print '<pre>';
print_r($venue);
print '</pre>';
?>