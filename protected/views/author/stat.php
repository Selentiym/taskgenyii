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
?>
<table>
    <tr><td>Всего символов</td><td><?php echo $model -> symbols(); ?></td></tr>
    <tr><td>Символов не оплачено</td><td><?php echo $model -> symbolsNotPayed(); ?></td></tr>
    <tr><td>Завершенных заданий</td><td><?php echo $model -> completedTasksNum; ?></td></tr>
    <tr><td>Принятых не с первого раза заданий</td><td><?php echo count($model -> secondlyAccepted); ?></td></tr>
</table>
<?php
var_dump($model -> secondlyAccepted);
foreach ($model -> completedTasks as $task) {
    if (count($task -> notAcceptedTexts) > 0) {
        echo $task -> name.'<br/>';
    }
}
?>