<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 10:50
 */
/**
 * @type Author $model
 */
$this -> renderPartial('//_navBar');
foreach($model -> tasks as $task) {
    $this -> renderPartial('//task/shortcut', array('task' => $task));
}