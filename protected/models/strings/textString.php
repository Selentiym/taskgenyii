<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.07.2016
 * Time: 10:23
 */
class textString {

    const quatation = "[^а-яa-z\d\s\"\']+";

    public $initial;
    public $text;
    public $cachedText;

    /**
     * @var wordSet[] $sentences
     */
    public $sentences;
    /**
     * @var array $subs - содержит список замен. Нужен для возврата точных совпадений обратно
     */
    public $subs = array();
    public $prepared = false;

    const END_SENTENCE = 'ends';
    public function __construct($text){
        $this -> cachedText = $text;
        $this -> text = $text;
        //Заменяем переносы строк и концы параграфа концами предложений.
        //Специально делаем это до каких-либо проверок.
        $this -> text = trim(preg_replace('/<p[^<>]*>/u', self::END_SENTENCE, $this -> text));
        $this -> text = trim(preg_replace('|<\/p[^<>]*>|u', self::END_SENTENCE, $this -> text));
        $this -> text = trim(preg_replace('/<(\/?)br[^<>]*>/u', self::END_SENTENCE, $this -> text));
        //Убрали теги
        $this -> text = trim(strip_tags($this -> text));
    }
    public function getSentenceTexts(){
        $t = $this -> text;
        //$this -> text = $this -> cachedText;

        //очищаем от непонятных спецсимволов
        $this -> text = trim(preg_replace('/\&[a-zA-Z]+\;/u', ' ', $this -> text));
        //Перенос строки - вообще-то тоже конец предложения.
        $this -> text = trim(preg_replace('/\n+/u', self::END_SENTENCE, $this -> text));
        //Чтобы санкт-петербург не сливался в одно слово
        $this -> text = str_replace('-',' ',$this -> text);
        //Ковычки нам не важны
        $this -> text = preg_replace("/[\"\'\`]/iu"," ",$this -> text);
        //Удаляем лишние пробелы и переносы строк
        $this -> text = trim(preg_replace("/(?<=\S)\s+(?=\S)/u", " ", $this -> text));

        //Удалили все внутренние точки
        $this -> text = preg_replace("/\.(?=\w)/u","",$this -> text);
        //короткие слова чаще всего являются сокращениями.
        $this -> text = preg_replace("/(?<=\s(\w){1})\./u","",$this -> text);
        $this -> text = preg_replace("/(?<=\s(\w){2})\./u","",$this -> text);
        /**
         * блок частых сокращений
         */
        $replace = [
            "корп." => "корп",
            "лит." => "лит"
        ];
        str_replace(array_keys($replace), array_values($replace), $this -> text);
        //знаки препинания. Разделителеми предложений будут только ?! и точка
        $this -> text = trim(preg_replace ("/[.?!]+/u", self::END_SENTENCE, $this -> text));
        //Добавляем основной разделитель предложения.
        $this -> text = str_replace(". ",self::END_SENTENCE, $this -> text);
        $this -> text = trim(preg_replace("/(\s*".self::END_SENTENCE."\s*)+/u",self::END_SENTENCE,$this -> text));

        //Делаем знаки препинания отдельными словами
        $this -> text = preg_replace("/(".self::quatation.")/iu"," $1 ",$this -> text);
        return array_map("trim",explode(self::END_SENTENCE, $this -> text));
    }
    public function addNewSentence($text){
        if ($text) {
            $this -> sentences [] = new wordSet($text);
        }
    }
    public function prepare(){
        if (!$this -> prepared) {



            $this -> sentences = array();
            $sentences = $this -> getSentenceTexts();
            array_map([$this,"addNewSentence"], $sentences);
            $this -> prepared = true;
        }
    }
    public function getToPrint(){
        if (empty($this -> sentences)) {
            $this -> prepare();
        }
        $rez = implode('. ',array_map(function($sent){ return $sent -> getToPrint();}, $this -> sentences));
        foreach ($this -> subs as $key => $value) {
            $rez = str_replace($key, $value, $rez);
        }
        return $rez;
    }

    /**
     * Ищет как угодно разбитые фразы в пределах одного предложения.
     * @param wordSet $needle
     * @return int
     */
    public function lookForSentenced(wordSet $needle){
        $this -> prepare();
        $count = 0;
        foreach ($this -> sentences as $sent) {
            $count += $sent -> lookFor($needle);
        }
        return $count;
    }
    /**
     * Ищет фразы, разбитые только лишь предлогами.
     * @param wordSet $needle
     * @return int
     */
    public function lookForSentencedGlued(wordSet $needle){
        $this -> prepareGlued();
        $count = 0;
        foreach ($this -> sentences as $sent) {
            $count += $sent -> lookForGlued($needle);
        }
        return $count;
    }
    /**
     * Ищет полное вхождение
     * @param $string
     * @return int
     */
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