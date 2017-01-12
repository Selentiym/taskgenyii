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
//$this -> renderPartial('//_navBar');
/**$rez = array_reduce($model -> completedTasks, function($prev, $task){
    return $prev + $task -> rezult -> length;
}, 0);*/

echo "Общее число символов: ".$model -> symbols();
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
<?php
echo "<ul>";
define('completedAmount', 5);
foreach(array_slice($model -> completedTasks,completedAmount) as $task) {
    $this -> renderPartial('//task/shortcut', array('task' => $task));
}
echo "</ul>";
if (count($model -> completedTasks) >  completedAmount):
?>
<div><a href="<?php echo Yii::app() -> createUrl('cabinet/archive'); ?>">Показать все</a></div>
<h3>Статистика</h3>
<?php endif;
$this -> renderPartial('//author/stat',['model' => $model]);
//$this -> renderPartial('//cabinet/dialog', array('model' => User::model() -> findByPk(2)));
?>
