<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 19.09.2016
 * Time: 21:09
 */
class wordSetGlued extends wordSet {
    public function __construct($string, $param = NULL) {
        parent::__construct($string, $param);
        sort($this -> stems, SORT_STRING);
    }
}