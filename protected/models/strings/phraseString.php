<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 11:15
 */
class phraseString extends metricString {
    public $initial;
    public function __construct($string, $num){
        parent::__construct($string, $num);
        $this -> initial = $string;
    }
}