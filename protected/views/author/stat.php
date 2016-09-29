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
    echo UHtml::link("Редактировать", Yii::app()->createUrl('cabinet/authorEdit', ['arg' => $model->id]), ['class' => 'buttonText']);
}
echo Yii::app() -> user -> getFlash('noUnpayedTasks');
?>
<table>
    <tr><td>Всего символов</td><td><?php echo $model -> symbols(); ?></td></tr>
    <tr><td>Символов не оплачено</td><td><?php echo $model -> symbolsNotPayed(); ?></td></tr>
    <tr><td>Завершенных заданий</td><td><?php echo $model -> completedTasksNum; ?></td></tr>
    <tr><td>Принятых не с первого раза заданий</td><td><?php echo count($model -> secondlyAcceptedIds()); ?></td></tr>
</table>
<?php
    echo CHtml::link("Уведомить об оплате",Yii::app() -> createUrl("cabinet/authorPay",['arg' => $model -> id]), ['class' => 'buttonText']);
?>