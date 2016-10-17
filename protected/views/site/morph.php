<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.07.2016
 * Time: 12:29
 */
$data = $_GET;
$word = new eArrayString($data['text']);
$word -> prepare();
$rez = json_encode(array('stems' => $word -> stems, 'eStems' => $word -> eStems));
echo $rez;