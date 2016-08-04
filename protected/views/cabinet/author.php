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
?>
<h2>Активные задания</h2>
<ul>
    <?php
    foreach($model -> activeTasks as $task) {
        $this -> renderPartial('//task/shortcut', array('task' => $task));
    }
    ?>
</ul>

<h2>Завершенные задания</h2>
<ul>
    <?php
    foreach($model -> completedTasks as $task) {
        $this -> renderPartial('//task/shortcut', array('task' => $task));
    }
    ?>
</ul>
