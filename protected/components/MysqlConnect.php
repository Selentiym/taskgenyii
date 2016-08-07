<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 15.04.2016
 * Time: 19:33
 */
class MysqlConnect {
    protected static $_instance;
    protected $connection = false;

    private function __construct() {
        $this -> connection = require_once('database.pss.php');
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
            if (!self::$_instance -> connection) {
                self::$_instance = null;
            }
        }
        return self::$_instance;
    }
    public static function getConnection(){
        $inst = self::getInstance();
        if ($inst) {
            return $inst -> connection;
        } else {
            throw new DatabaseException('Could not connect to the database!');
        }
    }

    private function __clone() {
    }

    private function __wakeup() {
    }
}
class DatabaseException extends Exception {

}
?>