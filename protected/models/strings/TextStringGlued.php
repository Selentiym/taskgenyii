<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 23.09.2016
 * Time: 21:15
 */
class TextStringGlued extends textString{
    public function addNewSentence($text){
        if ($text) {
            $this -> sentences [] = new wordSetGlued($text);
        }
    }
}