<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.07.2016
 * Time: 11:13
 */
$user = User::logged();
?>
<div style="text-align:right;">
    Вы вошли как <?php echo UHtml::link($user -> username, Yii::app() -> createUrl('cabinet/index')).'. '.UHtml::link('Выход', Yii::app() -> createUrl('login/logout')); ?>
</div>
