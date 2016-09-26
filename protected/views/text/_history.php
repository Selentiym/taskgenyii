<?php
/**
 * @type Text $model
 */
?>
<div class="textHistoryShortcut">
    <div class="head">
        <?php $this -> renderPartial("//text/_infrobar", ['model' => $model]); ?>
    </div>

    <?php if (Yii::app()-> user -> checkAccess('editor')) :
        $task = $model -> task;
        $buttons = true;
        Yii::app()->getClientScript()->registerScript('redirectScript', "
        $('#redirectToKeywords').click(function(){
            location.href='".Yii::app() -> createUrl('cabinet/loadKeywords',['arg' => $task -> id])."';
        });
        $('#redirectToEditTask').click(function(){
            location.href='".Yii::app() -> createUrl('task/edit',['arg' => $task -> id])."';
        });
    ", CClientScript::POS_END);
        ?>
        <input type="<?php echo $buttons ? 'button' : 'submit'; ?>" name="redirectToEditTask" id="redirectToEditTask" value="Редактировать задание" />
        <input type="<?php echo $buttons ? 'button' : 'submit'; ?>" name="redirectToKeywords" id="redirectToKeywords" value="Загрузить данные из KeyCollector" />
        <?php
    endif; ?>
    <div class="body">
        <?php echo $model -> text; ?>
    </div>
    <?php $this -> renderPartial('//comment/_comments', ['model' => $model]); ?>
</div>
