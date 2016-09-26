<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 10:10
 */
$data = $_POST;
?>

<style>
    form textarea {
        width:500px;
        height:400px;
        display:inline-block;
        margin-right:40px;
    }
</style>
В левое окно вставляется список поисковых фраз из KeyCollector - все пять столбцов, включая 3 вида статистики. (правда реально пока что используется только одна - точные совпадения)<br/>
В правое окно вставляется список групп после анализа групп в KeyCollector - два столбца.<br/>
<?php //echo Yii::app() -> urlManager -> createUrl('cabinet/TaskCreate', array_filter(['parentId' => $_GET['parentId']])); ?>
<form method="post">
    <textarea name="Task[input_search]"><?php echo $data['Task[input_search]']; ?></textarea>
    <textarea name="Task[keystring]"><?php echo $data['Task[keystring]']; ?></textarea>
    <div>
        <input type="<?php echo $buttons ? 'button' : 'submit'; ?>" name="redirectToShowTask" id="redirectToShowTask" value="Сохранить и перейти к просмотру текста" />
        <input type="<?php echo $buttons ? 'button' : 'submit'; ?>" name="redirectToEditTask" id="redirectToEditTask" value="Сохранить и вернуться к редактированию" />
    </div>
    <!--<input type="submit" value="Сохранить и перейти в кабинет" name="toCabinet"/>
    <input type="submit" value="Сохранить и составить ТЗ" name="editTask"/>-->
</form>

