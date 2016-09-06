<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 31.08.2016
 * Time: 17:08
 */
/**
 * @type Task $task
 * @type bool $buttons
 */
if ($buttons) {
    Yii::app()->getClientScript()->registerScript('redirectScript', "
        $('#redirectToKeywords').click(function(){
            location.href='".Yii::app() -> createUrl('cabinet/loadKeywords',['arg' => $task -> id])."';
        });
        $('#redirectToEditTask').click(function(){
            location.href='".Yii::app() -> createUrl('task/edit',['arg' => $task -> id])."';
        });
        $('#redirectToShowTask').click(function(){
            location.href='".Yii::app() -> createUrl('task/view',['arg' => $task -> id])."';
        });
    ", CClientScript::POS_END);
}
?>
<input type="<?php echo $buttons ? 'button' : 'submit'; ?>" name="redirectToShowTask" id="redirectToShowTask" value="Смотреть результат" />
<input type="<?php echo $buttons ? 'button' : 'submit'; ?>" name="redirectToEditTask" id="redirectToEditTask" value="Редактировать задание" />
<input type="<?php echo $buttons ? 'button' : 'submit'; ?>" name="redirectToKeywords" id="redirectToKeywords" value="Загрузить данные из KeyCollector" />
