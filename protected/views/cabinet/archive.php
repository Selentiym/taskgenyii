<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 07.08.2016
 * Time: 15:41
 */
/**
 * @type Author $model
 * @type CabinetController $this
 */
//$this -> renderPartial('//_navBar');
foreach ($model -> completedTasks as  $task) {
    $this -> renderPartial('//task/shortcut',['model' => $task]);
}
?>

