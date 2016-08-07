<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.08.2016
 * Time: 15:34
 */
namespace Shingles;
class Fast {

    public static $shingleLength = 2;

    const addingsCount = 1000;
    const checkLength = 100;
    const remainderHashFunc = 'md5';
    const delimiter = ';';
    public static $hashFunctions = array(
        ['md5',''], ['sha1',''],['md2',''],['md4',''],['sha256',''],['sha512','']
    );
    public static $addings;
    /**
     * @var string $_text contains cleared text
     */
    private $_text = '';
    /**
     * @var array[self::checkLength] $_shingles - minimal shingles for every hash function
     */
    private $_shingles = [];
    /**
     * @return int[self::checkLength] - self::checkLength minimal shingles for every hash function
     */
    private function calculateShingles(){
        $this -> _text = \arrayString::removeRubbishFromString($this -> _text);
        $this->_shingles = [];
        $words = explode(' ', $this -> _text);
        $shingles = [];
        $count = 0;
        foreach ($words as $key => $word) {
            $shingles[] = implode(' ',array_slice($words,$key, self::$shingleLength));
            $count ++;
            if ($count == count($words) - self::$shingleLength + 1) {
                break;
            }
        }

        $func = reset(self::$hashFunctions);
        $addings = self::giveAddings();
        for ($i = 0; $i < self::checkLength; $i++) {
            $min = false;
            foreach($shingles as $shingle) {
                $hash = hash($func[0], $func[1].$shingle);
                if ((!$min)||($hash < $min)) {
                    $min = $hash;
                }
            }
            $func = next(self::$hashFunctions);
            if (!$func) {
                $func = [self::remainderHashFunc,next($addings)];
            }
            $this -> _shingles [] = $min;
        }
        ///var_dump($this -> _shingles);
    }

    /**
     * Shingles constructor. Can obtain either finalized hashes of the text or the text itself.
     * Note: it is supposed that ready minimal hashes are stored in database, so the process
     * of generating hashes may take long since it is only done once for the text.
     * @param int[self::checkLength]|string $input
     * @param bool $explode - whether input string is
     */
    public function __construct($input, $explode = false){
        if (is_array($input) == self::checkLength) {
            $this -> _shingles = $input;
        } else {
            if ($explode) {
                $this->_shingles = explode(self::delimiter, $input);
            } else {
                $this->_text = $input;
                $this->calculateShingles();
            }
        }
    }
    public function compare(Fast $shingle){
        $external = $shingle -> giveShingles();
        $internal = $this -> giveShingles();
        if (count($external) > count($internal)) {
            $shorter = $internal;
            $longer = $external;
        } else {
            $longer = $internal;
            $shorter = $external;
        }
        $max = count($shorter);
        if ($max <= 0) {
            return 0;
        }
        $sim = 0;
        for ($i=0; $i < $max; $i++) {
            if ($shorter[$i] == $longer[$i]) {
                $sim ++;
            }
        }

        return $sim / $max;
    }
    public function giveShingles(){
        return $this -> _shingles;
    }
    public static function giveAddings(){
        if (!self::$addings) {
            self::$addings = [];
            for ($i = 0; $i < self::addingsCount; $i++) {
                self::$addings[] = crc32($i);
            }
        }
        return self::$addings;
    }

    /**
     * @return string - a string containing all the data which to be saved in database
     */
    public function archive(){
        return implode(self::delimiter, $this -> _shingles);
    }
}