<?php
/**
 * @type Task $task
 */
?>
<li>
<a href="<?php echo Yii::app() -> createUrl('task/view', array('arg' => $task -> id)); ?>"><?php echo $task -> name; ?></a>
</li>
