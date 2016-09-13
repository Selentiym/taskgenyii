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
        $this -> renderPartial ('//task/_buttons', ['task' => $model -> task, 'buttons' => true]);
    endif; ?>
    <div class="body">
        <?php echo $model -> text; ?>
    </div>
    <?php $this -> renderPartial('//comment/_comments', ['model' => $model]); ?>
</div>
