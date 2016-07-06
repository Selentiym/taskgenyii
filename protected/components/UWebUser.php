<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 12:48
 */
class UWebUser extends CWebUser {
    public function onLogout(CEvent $event){
        echo "123";
    }
}