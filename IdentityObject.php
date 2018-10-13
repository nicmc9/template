<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12.10.2018
 * Time: 14:42
 */

namespace woo\mapper;

//Шаблон состоит из набора методов которые можно вызывать для построения критерия запроса
// определив состояние объекта вы можете передать его методы который отвечает за создание SQL Оператора

class IdentityObject{
    private $name = null;

    /**
     * @param null $name
     */
    public function setName($name): void
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


class EventIdentityObject extends IdentityObject{
    private $start = null;
    private $minstart = null;

    function setMinimumStart($minstart){
        $this->minstart = $minstart;
    }
    function getMinimumStart(){
       return $this->minstart;
    }

    /**
     * @param null $starts
     */
    public function setStart($start): void
    {
        $this->start = $start;
    }

    /**
     * @return null
     */
    public function getStart()
    {
        return $this->start;
    }

}

$idobj = new EventIdentityObject();
$idobj->setMinimumStart(time());
$idobj->setName("A fine Show");

$comps = array();
$name = $idobj->getName();
if(!is_null($name)) {
    $comps[] = " name = '{$name}'";
}

$minstart = $idobj->getMinimumStart();
if(!is_null($minstart)) {
    $comps[] = " start > '{$minstart}'";
}


$start = $idobj->getStart();
if(!is_null($start)) {
    $comps[] = " start = '{$start}'";
}
$clause = 'WHERE'.implode("and",$comps);


print $clause;

// Можно создать много замечательной логики по построению запросов
// главное создать

