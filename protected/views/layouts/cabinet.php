<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 10:11
 */
Yii::app() -> getClientScript() -> registerCoreScript('jquery');
//Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl.'/js/jquery.min.js',CClientScript::POS_BEGIN);
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

echo $content;