<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.07.2016
 * Time: 12:29
 */
/**
 * @type Text $model
 * @type Text $text - alias for $model
 */
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/write.js', CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/underscore.js', CClientScript::POS_BEGIN);
Yii::app() -> getClientScript() -> registerScript('check',"
    window.textObj = new text(".$model->id.");
    var timerId = setInterval(function(){
    }, 1000);

", CClientScript::POS_READY);

$text = $model;
?>
<input type="button" value="Проверить" id="check"/>
<input type="button" value="Уникальность" id="uniqueButton"/>
<div>
    <div style="width:59%; display: inline-block">
    <?php
    $this->widget('application.extensions.tinymce.TinyMce',
        array(
            'model'=>$model,
            'attribute'=>'text',
            'settings' => array(
                'setup' => 'js:function(ed) {
                ed.on("change", function(ed) {
                    textObj.contentChanged(); // get actual content
                })}'
            )
        ));
    ?>
    </div>
    <div style="width:39%; display:inline-block;vertical-align:top">
        <div id="unique"></div>
        <div id="seoData"></div>
        <div>
            <?php $this -> renderPartial('//pattern/keys', array('data' => $model -> task -> keyphrases)); ?>
        </div>
        <div id="rezultDiv">Текст с подсвеченными ключевыми словами: <br/></div>
    </div>
</div>