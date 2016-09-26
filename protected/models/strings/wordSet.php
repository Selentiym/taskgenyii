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
        $string = str_replace('-',' ', $string);
        $this -> words = array_map(function($word){
            $temp = new word(trim($word));
            if ($temp -> stem) {
                $this -> stems[] = $temp -> stem;
            }
            return $temp;
        },explode(' ', $string));
        //Понадобится, когда будем считать знаки препинания тоже словами.
        //[^a-zA-Z\d "']
    }
    public function getToPrint(){
        return implode(' ', array_map(function($w){ return $w -> getToPrint();},$this -> words));
    }
    public function lookFor(wordSet $needle){
        $intersect = array_unique(array_intersect($this ->stems, $needle -> stems));
        $amount = count($intersect);

        //Если фраза есть внутри, то нужно подсветить ее
        if ($amount == count($needle -> stems)) {
            //Пробегаем по всем корням искомой фразы
            foreach ($needle -> words as $search) {
                if (!$search -> stem) {
                    continue;
                }
                //Пробегаем по всем корням предложения
                foreach ($this -> words as $key => $word) {
                    if ($word -> used) {
                        continue;
                    }
                    if ($word -> stem == $search -> stem) {
                        $word -> makeUse('green',$needle -> param);
                        //удаляем корень, чтобы в разных ключевых фразах не
                        // было использовано одно слово
                        unset($this -> stems[$key]);
                        break;
                    }
                }
            }
            return 1;
        }
        return 0;
    }
}