<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.10.2016
 * Time: 19:48
 */
class eArrayString extends arrayString {
    public $eStems = [];
    public function findStems(){
        $this -> stems = [];
        foreach (array_map('trim',explode(' ', $this -> text)) as $word) {
            $stem = mb_strtolower(Stemmer::getInstance() -> stem_word ($word), 'utf-8');
            $this -> stems[] = $stem;
            $this -> eStems[$stem] = $word;
        }
    }
}