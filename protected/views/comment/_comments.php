<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 01.08.2016
 * Time: 14:55
 */
/**
 * @type Commentable $model
 */
?>
<div class="comments">
    <form class="commentForm">
        <input type="hidden" value="<?php echo $model -> CommentId(); ?>" name="Comment[id_obj_type]"/>
        <input type="hidden" value="<?php echo $model -> id; ?>" name="Comment[id_obj]"/>
        <textarea style="width:400px; height:100px" name="Comment[comment]" placeholder="Введите текст комментария"></textarea><br/>
        <input type="button" value="Отправить комментарий" class="goComment"/>
    </form>
    <div class="olderOnes">
    <?php
    $view = $model -> CommentView();
    foreach ($model -> comments as $comment) {
        $this -> renderPartial($view, array('model' => $comment));
    }
    ?>
    </div>

</div>
<?php Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl.'/js/comment.js', CClientScript::POS_END); ?>
<?php Yii::app() -> getClientScript() -> registerCssFile(Yii::app() -> baseUrl.'/css/comment.css'); ?>