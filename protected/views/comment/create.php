<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 01.08.2016
 * Time: 16:01
 */
/**
 * @type Comment $model
 */
$class = Commentable::$types[$model -> id_obj_type];
$this -> renderPartial($class::model() -> findByPk($model -> id_obj) -> CommentView(),array('model' => $model));
?>