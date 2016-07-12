<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.07.2016
 * Time: 10:31
 */
class wordSet {
    /**
     * @var word[] $words
     */
    public $words;
    public $stems = array();
    public $param = 0;
    public function __construct($string, $param = NULL){
        if ($param) {
            $this -> param = $param;
        }
        $this -> words = array_map(function($word){
            $temp = new word(trim($word));
            if ($temp -> stem) {
                $this -> stems[] = $temp -> stem;
            }
            return $temp;
        },explode(' ', $string));
    }
    public function getToPrint(){
        return implode(' ', array_map(function($w){ return $w -> getToPrint();},$this -> words));
    }
    public function lookFor($needle){
        $amount = count(array_intersect($this ->stems, $needle -> stems));
        //Если фраза есть внутри, то нужно подсветить ее
        if ($amount == count($needle -> stems)) {
            foreach ($needle -> words as $search) {
                if (!$search -> stem) {
                    continue;
                }
                foreach ($this -> words as $word) {
                    if ($word -> used) {
                        continue;
                    }
                    if ($word -> stem == $search -> stem) {
                        $word -> makeUse('green',$needle -> param);

                        break;
                    }
                }
            }
            return 1;
        }
        return 0;
    }
}