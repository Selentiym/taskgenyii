<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 11:53
 */
require_once(Yii::getPathOfAlias('application').'/../vendor'. DIRECTORY_SEPARATOR .'autoload.php');
class MorphySing {
    private static $instance = false;
    private $morphy;
    private function __construct(){
        $dir = 'vendor/umisoft/phpmorphy/dicts/';
        //$opt = array('storage' => PHPMORPHY_STORAGE_SHM);
        $lang = 'ru_RU';
        try {
            $morphy = new phpMorphy($dir, $lang);//, $opts);
            $this -> morphy = $morphy;
        } catch (phpMorphy_Exception $e) {
            die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
        }
    }
    public static function getInstance(){
        if (!self::$instance) {
            $temp = new self();
            self::$instance = $temp -> morphy;
        }
        return self::$instance;
    }
}