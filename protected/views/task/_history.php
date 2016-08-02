<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.08.2016
 * Time: 12:24
 */
/**
 * @type Task $model
 */
Yii::app() -> getClientScript() -> registerCssFile(Yii::app() -> baseUrl . '/css/history.css');
$this -> renderPartial('//_navBar');

foreach ($model -> texts as $text) {
    if (($text -> id == $model -> currentText -> id)&&(!$model -> rezult)) {
        $view = '//text/write';
    } else {
        $view = '//text/_history';
    }
    $this -> renderPartial($view,['model' => $text]);
}
$this -> renderPartial('//pattern/common',['model' => $model]);
?>

