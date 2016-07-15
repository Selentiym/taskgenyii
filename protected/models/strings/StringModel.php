<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.07.2016
 * Time: 11:06
 */
abstract class StringModel extends UModel {
    /**
     * @var arrayString $arrayString - contains an object with
     * stems and all the other info
     */
    public $arrayString;
    /**
     * @var string $lemma
     */
    public $lemma;
    /**
     * @return string[] - array of stems of the keyword. If everything is all
     * right, contains an array of matching stems
     */
    public function giveStems(){
        return $this -> giveArrayString() -> stems;
    }

    /**
     * @return string - the standard form of the first word
     */
    public function giveLemma(){
        if (!$this -> lemma) {
            $this -> lemma = $this -> giveArrayString() -> lemma();
        }
        return $this -> lemma;
    }
    private function giveArrayString(){
        if (!$this -> arrayString) {
            $attr = static::stringAttribute();
            $this -> arrayString = new arrayString($this -> $attr);
            $this -> arrayString -> prepare();
        }
        return $this -> arrayString;
    }

    /**
     * @return string - name of the attribute
     */
    public abstract function stringAttribute();
}