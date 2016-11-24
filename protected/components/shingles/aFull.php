<?php
namespace Shingles;
abstract class aFull
{
    const shingleLength = 3;
    public $dump;
    /**
     * @var integer
     */
    protected $shinglesCount;

    private $_text;
    private $_shingles;

    /**
     * @param string $input
     */
    public function __construct($input)
    {
        $this -> _text = $input;
    }

    /**
     * @return array
     */
    public function giveShingles()
    {
        if (!is_array($this -> _shingles)) {
            $this -> clearText();
            $this -> _shingles = $this -> splitShingles(array_filter(explode(' ', $this -> _text)));
        }
        return $this -> _shingles;
    }

    private function clearText() {
        $this -> _text = \arrayString::removeRubbishFromString($this -> _text);
    }
    /**
     * @param array $words
     * @return array
     */
    private function splitShingles(array $words)
    {
        $shingles = array();

        $countWords = count($words);

        /**
         * Вычитаю кол-во слов так как я их итак собираю перед хешом
         */
        for ($i = 0; $i <=  $countWords-self::shingleLength; $i++) {
            $string = implode(' ', array_slice($words, $i, self::shingleLength));
            $shingles[] = [$this->makeHash($string),$string, $i];
        }

        return $shingles;
    }

    /**
     * @param string $shingle
     * @return mixed
     */
    abstract public function makeHash($shingle);

    /**
     * @param aFull $shingle
     * @return float
     */
    public function compare(aFull $shingle)
    {
        $shingle1 = $this->giveShingles();
        $shingle2 = $shingle->giveShingles();
        $common = array_uintersect($shingle1, $shingle2, function($s1,$s2){
            if ($s1[0] == $s2[0]) {
                return 0;
            }
            if ($s1[0] > $s2[0]) {
                return 1;
            }
            return -1;
        });
        //$diff = array_diff($shingle1, $shingle2);
        $count_shingle = count($shingle1);
        $thisCount = count($shingle2);
        $dump = [];
        /*$dump = array_map(function($el){
            return trim($el[1]);
        }, $common);*/
        foreach ($common as $el) {
            $dump [$el[2]] = $el[1];
        }
        $this -> dump = $dump;
        $shingle -> dump = $this -> dump;
        return (count($common))/min($count_shingle, $thisCount)*100;
    }
}
