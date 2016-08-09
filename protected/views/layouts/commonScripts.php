<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.08.2016
 * Time: 14:30
 */
Yii::app() -> getClientScript() -> registerCoreScript('jquery');
//Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl.'/js/jquery.min.js',CClientScript::POS_BEGIN);
Yii::app() -> getClientScript() -> registerCssFile(Yii::app() -> baseUrl.'/css/common.css');
Yii::app() -> getClientScript() -> registerScript('baseUrlDefine',"baseUrl = '".Yii::app() -> baseUrl."';",CClientScript::POS_BEGIN);
Yii::app() -> getClientScript() -> registerScript('buttonScript',"
var _pressedButton = undefined;
$('body').keypress(function(e){
    _pressedButton = e.which;
    //alert(_pressedButton);
});
$('body').keyup(function(e){
    _pressedButton = undefined;
});
",CClientScript::POS_END);

Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/jquery-ui.min.js', CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScript('draggable',"
    $('.toDrag').draggable();
",CClientScript::POS_READY);
?>