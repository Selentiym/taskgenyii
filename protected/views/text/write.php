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
$handInErr = Yii::app() -> user -> getFlash('textHandIn');
?>


    <div style="width:59%; display: inline-block">
    <form method="post" id="textForm" data-id="<?php echo $model -> id; ?>">

        <?php if (!Yii::app()-> user -> checkAccess('editor')) :?>
        <input type="button" value="Сдать" id="send"/>

        <input type="button" value="Попросить принять" id="sendWithMistakes"/>

        <?php endif; ?>

        <?php $this -> renderPartial("//text/_infrobar", ['model' => $text]); ?>
        <?php if (Yii::app()-> user -> checkAccess('editor')) : ?>
            <input type="button" value="Принять" id="accept"/>
            <input type="button" value="Отклонить" id="decline"/>
        <?php endif; ?>
        <input type="button" value="Проверить seo, keys" id="check"/>
        <input type="button" value="Проверить уникальность" id="uniqueButton"/>

        <?php if (Yii::app()-> user -> checkAccess('editor')) :
            $this -> renderPartial ('//task/_buttons', ['task' => $model -> task, 'buttons' => true]);
        endif; ?>
        <div id="editorBlock">
        <?php
        $this->widget('application.extensions.tinymce.TinyMce',
            array(
                'model'=>$model,
                'attribute'=>'text',
                'settings' => array(
                    'entity_encoding' => 'raw',
                    'setup' => 'js:function(ed) {
                    ed.on("change", function(ed) {
                        textObj.contentChanged(); // get actual content
                    })}',
                    'oninit' => 'js:function(){
                    textObj.analyze();
                    }'
                )
            ));
        echo UHtml::activeTextArea($model,'description',['class' => 'description','placeholder' => 'Описание текста, показываемое при поиске.','id' => 'description']);
        ?>
        </div>
        </form>
        <?php
        echo $handInErr;
        $this -> renderPartial('//comment/_comments',array('model' => $model));
        ?>
    </div>
    <div style="width:39%; display:inline-block;vertical-align:top">
        <div id="unique"></div>
        <div id="crossUnique"></div>
        Длина:
        <div id="lengthContainer" <?php if ($model -> task -> max_length > 0) echo "data-max='".$model->task->max_length."'"; ?> <?php if ($model -> task -> min_length > 0) echo "data-min='".$model->task->min_length."'"; ?>>
            <span id="length"><?php echo $model -> length; ?></span>
        </div>
        <div id="seoData"></div>
        <div>
            <?php $this -> renderPartial('//pattern/keys', array('data' => $model -> task -> getKeyphrasesSorted())); ?>
        </div>
        <div id="rezultDiv">Текст с подсвеченными ключевыми словами: <br/></div>
    </div>


