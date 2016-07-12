<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.07.2016
 * Time: 9:15
 */
class sentenceString extends arrayString {
    /**
     * @var arrayString[] $sentences
     */
    public $sentences = array();

    public function __construct($text){
        parent::__construct($text);
        //Разбиваем текст на предложения.
        $this -> sentences = array();
        array_map(function($str){
            $str = trim($str);
            if (strlen($str) > 1) {
                $this -> sentences [] = new arrayString($str);
                return true;
            }
            return false;
        }, explode('.', $text));
    }

    /**
     * @param arrayString $needle
     * @return int - how many times did the $needle occur in this text
     */
    public function lookFor(arrayString $needle){
        $count = 0;
        foreach ($this -> sentences as $sent) {
            $count += $sent -> lookForUnordered($needle);
        }
        return $count;
    }
}