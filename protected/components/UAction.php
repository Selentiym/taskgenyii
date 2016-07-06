<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 11:59
 */
class UAction extends CAction {
    /**
     * @var bool|callable - whether to remove this page from history
     */
    public $ignore = false;
    /**
     * Убирает последний путь из истории
     */
    public function ignoreHist(){
        if ($this -> ignore) {
            Yii::app()->UrlHelper->ignore();
        }
    }
}