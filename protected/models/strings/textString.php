<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.07.2016
 * Time: 10:23
 */
class textString {
    public $initial;
    public $text;
    /**
     * @var wordSet[] $sentences
     */
    public $sentences;
    /**
     * @var array $subs - содержит список замен. Нужен для возврата точных совпадений обратно
     */
    public $subs = array();
    public $prepared = false;

    public function __construct($text){
        //очищаем от непонятных спецсимволов
        $this -> text = preg_replace('/\&[a-zA-Z]+\;/u', ' ', $text);
        //$this -> text = preg_replace('/\n?\r?/u', '', $this -> text);
        //Удаляем лишние пробелы и переносы строк
        $this -> text = preg_replace ("/(?<=\w)\s+(?=\w)/u", " ", $this -> text);
    }
    public function prepare(){
        if (!$this -> prepared) {
            //Чтобы санкт-петербург не сливался в одно слово
            $this -> text = str_replace('-',' ',$this -> text);
            //Оставляем только буквы, пробелы и точки
            $this -> text = preg_replace('/[^\s\w\.]/u', '', $this -> text);


            $this -> sentences = array();
            array_map(function($t){
                if ($t) {
                    $this -> sentences [] = new wordSet($t);
                }
            },explode('.', $this -> text));
            $this -> prepared = true;
        }
    }
    public function getToPrint(){
        $rez = implode('. ',array_map(function($sent){ return $sent -> getToPrint();}, $this -> sentences));
        foreach ($this -> subs as $key => $value) {
            $rez = str_replace($key, $value, $rez);
        }
        return $rez;
    }
    public function lookForSentenced(wordSet $needle){
        $this -> prepare();
        $count = 0;
        foreach ($this -> sentences as $sent) {
            $count += $sent -> lookFor($needle);
        }
        return $count;
    }
    public function lookForLiteral($string){
        //Это должно делаться до начала поиска
        if ($this -> prepared) {
            return 0;
        }
        $count = 0;
        $i = count($this -> subs);

        $this -> text = preg_replace('/'.$string.'/iu', 'key'.$i, $this -> text, -1, $count);
        $this -> subs ['key'.$i] = '<span style="color:red">'.$string.'</span>';
        return $count;
    }
}