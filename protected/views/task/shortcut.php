<?php
/**
 * @type Task $task
 */
?>
<div>
<a href="<?php echo Yii::app() -> createUrl('task/view', array('arg' => $task -> id)); ?>"><?php echo $task -> name; ?></a>
</div>
