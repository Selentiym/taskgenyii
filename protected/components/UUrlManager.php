<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 10:59
 */
class UUrlManager extends CUrlManager {
    public function parseUrl($request){
        //Перехватываем url
        Yii::app() -> UrlHelper -> process($request);
        //Выполняем стандартные действия
        return parent::parseUrl($request);
    }
}