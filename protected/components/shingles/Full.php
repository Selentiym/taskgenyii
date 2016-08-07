<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.08.2016
 * Time: 12:21
 */

namespace Shingles;


class Full extends aFull {
    /**
     * @param string $shingle
     * @return mixed
     */
    public function makeHash($shingle) {
        return md5($shingle);
    }

    /**
     * @param $content
     * @return array
     */
    public function canonize($content) {
        return explode(' ',mb_strtolower(\arrayString::removeRubbishFromString($content),'utf-8'));
    }
}