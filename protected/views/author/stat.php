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
$editor = Yii::app() -> user -> checkAccess('editor');
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
<h3>Тариф</h3>
<div>Стоимость работ на данный момент <strong><?php echo $model -> tax; ?></strong>руб/1000 символов без пробелов</div>
<?php if($editor): ?>
    <p><?php echo Yii::app() -> user -> getFlash('tax'); ?></p>
    <form method="post" action="<?php echo Yii::app() -> createUrl('cabinet/authorEditTax',['arg' => $model -> id]); ?>">
        <label>
            <span>Оплата за 1000 символов без пробелов</span><br/>
            <input type="number" name="tax" placeholder="Тариф" value="<?php echo $model -> tax; ?>"/>
        </label>
        <input type="submit" name="Author" value="Сохранить">
    </form>
<?php endif; ?>
<h3>Предоплата</h3>
    <div>Уже начислена предоплата <strong><?php echo $model -> prepayed; ?> руб</strong></div>
    <?php if($editor): ?>
    <p><?php echo Yii::app() -> user -> getFlash('prepay'); ?></p>
    <form method="post" action="<?php echo Yii::app() -> createUrl('cabinet/authorPrePay',['arg' => $model -> id]); ?>">
        <label>
            <span>Прибавится к уже имеющейся сумме. Учитывается при нажатии "Уведомить об оплате".</span><br/>
            <input type="number" name="prepay" placeholder="предоплата"/>
        </label>
        <input type="submit" name="goPrepay" value="Начислить предоплату">
    </form>
    <?php endif; ?>

<h3>Задания к оплате</h3>
<?php
    $tasks = $model -> notPayedTasks();
    $pay = $model -> CalculatePayment($tasks);
    $salary = $pay + $model -> prepayed;
    if ($pay < 0) {
        $prepayTaken = $salary;
    } else {
        $prepayTaken = $model -> prepayed;
    }
?>
<div>Выполнено заданий на сумму (список ниже): <strong><?php echo $salary; ?></strong> руб</div>
<div>Было оплачено заранее: <strong><?php echo $model -> prepayed; ?></strong> руб</div>
<div>К оплате (с учетом предоплаты в размере <strong><?php echo $prepayTaken; ?></strong> руб): <strong><?php echo $pay < 0 ? 0 : $pay; ?></strong> руб</div>
<div>Предоплаты останется: <strong><?php echo $model -> prepayed - $prepayTaken; ?></strong> руб</div>
    <p></p>
<?php
if (Yii::app() -> user -> checkAccess('editor')) {
    echo CHtml::link("Уведомить об оплате", Yii::app()->createUrl("cabinet/authorPay", ['arg' => $model->id]), ['class' => 'buttonText']);
}
//var_dump($model -> completedNotPayedTasks);
if (!empty($tasks)) {
    foreach ($tasks as $t) {
        $this->renderPartial('//task/shortcutWithLength', array('task' => $t));
    }
} else {
    echo "<p></p><div>Нет <strong>принятых</strong> неоплаченных заданий.</div>";
}
?>