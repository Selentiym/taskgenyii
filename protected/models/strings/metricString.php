<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 11:15
 */
class metricString extends arrayString {
    public $num;
    public function __construct($string,$num = -1){
        parent::__construct($string);
        $this -> num = $num;
    }
}