<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.07.2016
 * Time: 11:21
 */
/**
 * @type Task $model
 */
$user = User::logged();
if ($model -> author == $user) {
    //echo "<a class='button' href='".Yii::app() -> createUrl('task/makeTask',["arg" => $model -> id])."'>Выполнить</a>";
}
//$this -> renderPartial('//pattern/'.$model -> pattern -> view, array('model' => $model));
$data = $model -> keyphrases;
?>


<style>
    tr td {
        background: #ffeb9c;
    }
    tr:first-child {
        font-weight:bold;
        text-align:center;
    }
    tr:first-child td {
        padding:10px;
        background: #cccccc;
    }
</style>

<div><strong>Тема статьи: </strong> <?php echo $model -> name; ?></div>
<?php

if (!$model -> pattern -> byHtml) {
    $this->renderPartial('//pattern/' . $model->pattern->view, array('model' => $model));
} else {
    echo $model -> pattern -> html;
}
$common = Pattern::model() -> findByAttributes(['view' => 'common']);
if ($common instanceof Pattern) {
    echo $common -> html;
}
?>
<!--
<p class="MsoNormal">Наличие структуры призвано стандартизировать статьи данного
    типа для удобства пользователя. Поэтому если Вы чувствуете, что получается
    неинтересно/неинформативно/некрасиво и вообще как-то не так с точки зрения
    человека, зашедшего на сайт в поисках информации, не стесняйтесь прерваться и
    обсудить свои вопросы или предложения с нами, особенно на первых этапах. Все вопросы можно
    писать во встроенный чат или скайп.
</p>
-->