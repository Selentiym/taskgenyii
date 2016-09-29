<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.09.2016
 * Time: 18:36
 */
/**
 * @type Payment $model
 */
?>
<h2>Оплаченные задания: </h2>
<?php
    if (User::logged() -> id == reset($model -> tasks) -> id_author) {
        echo CHtml::link("Подтвердить полчение",Yii::app() -> createUrl("cabinet/payConfirm",['arg' => $model -> id]), ['class' => 'buttonText']);
    }
?>
<ul>
<?php
    foreach($model -> tasks as $task) {
        $this -> renderPartial('//task/shortcut',['task' => $task]);
    }
?>
</ul>