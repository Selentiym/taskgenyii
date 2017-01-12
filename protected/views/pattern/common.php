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
echo $model -> pattern -> renderOneself($this, $model);
?>