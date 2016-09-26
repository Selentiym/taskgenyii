<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 19.09.2016
 * Time: 21:09
 */
class wordSetGlued extends wordSet {
    public function lookFor(wordSet $needle) {
        $intersect = array_unique(array_intersect($this ->stems, $needle -> stems));
        $amount = count($intersect);

        //Если фраза есть внутри, то нужно подсветить ее
        if ($amount == count($needle -> stems)) {
            //Будет содержать слова, которые потом выделим, если найдутся все.
            $toSelect = [];
            //Будет содержать корни, которые еще нужно найти
            $toFindDefault = array_filter($needle -> stems);
            $toFind = $toFindDefault;
            $gotIt = false;
            foreach ($this -> words as $word) {
                $st = $word -> stem;
                //Если слово использовано, то цепочка рвется
                if ($word -> used) {
                    $toSelect = [];
                    $toFind = $toFindDefault;
                }
                //Если корень слова нулевой, то не обращаем просто внимания на него.
                if ($st) {
                    $ind = array_search($st, $toFind);
                    //Если нашли корень следующего слова в тех, что нужно найти
                    if ($ind !== false) {
                        unset($toFind[$ind]);
                        $toSelect[] = $word;
                    } else {
                        //Если же не нашли, значит слово лишнее => перезапуск
                        $toSelect = [];
                        $toFind = $toFindDefault;
                    }
                    if (empty($toFind)) {
                        $gotIt = true;
                        break;
                    }
                }
            }

            if ($gotIt) {
                foreach ($toSelect as $word) {
                    $word -> makeUse('green',$needle -> param);
                }
                return 1;
            }
        }
        return 0;
    }
}