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
<form action="<?php echo Yii::app() -> urlManager -> createUrl('cabinet/TaskCreate'); ?>" method="post">
    <textarea name="phrases"><?php echo $data['phrases']; ?></textarea>
    <textarea name="cluster"><?php echo $data['cluster']; ?></textarea>
    <input type="submit" value="Составить"/>
</form>

