<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.09.2016
 * Time: 19:41
 */
/**
 * @type Author $model
 */
if (Yii::app() -> user -> checkAccess('editor')) {
    echo "<h2>$model->name</h2>";
    echo UHtml::link("Редактировать", Yii::app()->createUrl('cabinet/authorEdit', ['arg' => $model->id]), ['class' => 'buttonText']);
}
echo Yii::app() -> user -> getFlash('noUnpayedTasks');
?>
<table>
    <tr><td>Всего символов</td><td><?php echo $model -> symbols(); ?></td></tr>
    <tr><td>Символов не оплачено</td><td><?php echo UHtml::link($model -> symbolsNotPayed(),'#' ); ?></td></tr>
    <tr><td>Завершенных заданий</td><td><?php echo $model -> completedTasksNum; ?></td></tr>
    <tr><td>Принятых не с первого раза заданий</td><td><?php echo count($model -> secondlyAcceptedIds()); ?></td></tr>
</table>
<h3>Задания к оплате</h3>
<?php
if (Yii::app() -> user -> checkAccess('editor')) {
    echo CHtml::link("Уведомить об оплате", Yii::app()->createUrl("cabinet/authorPay", ['arg' => $model->id]), ['class' => 'buttonText']);
}
//var_dump($model -> completedNotPayedTasks);
foreach ($model -> completedNotPayedTasks as $t) {
    $this -> renderPartial('//task/shortcutWithLength', array('task' => $t));
}
?>