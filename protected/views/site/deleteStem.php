<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 27.09.2016
 * Time: 18:58
 */
$words = array_map('trim',explode(' ',$_POST["text"]));
$was = count($words);
$findStem = mb_strtoupper($_POST["stem"],"UTF-8");
$words = array_filter($words, function ($word) use ($findStem) {
    $stem = Stemmer::getInstance() -> stem_word($word);
    if ($stem == $findStem) {
        return false;
    }
    return true;
});
$rez['rezText'] = implode(" ",$words);
$rez['success'] = $was > count($words);
echo json_encode($rez);
