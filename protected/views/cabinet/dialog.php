<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 07.08.2016
 * Time: 17:16
 */
/**
 * @type User $model
 * @type string $id
 */
if (!$id) {
    $id = $model -> username;
}
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl. '/js/dialog.js',CClientScript::POS_END);
Yii::app() -> getClientScript() -> registercssFile(Yii::app() -> baseUrl. '/css/dialog.css');

Yii::app() -> getClientScript() -> registerScript('dialog'.$id,
    "new Dialog($('#$id'),".Yii::app()->user->getId().",$model->id,'".date('Y-m-d H:i:s')."');",CClientScript::POS_READY);

?>
<div class="dialog_shortcut" id="<?php echo $id; ?>_shortcut">

</div>
<div class="toDrag" style="z-index:5;position:absolute;">
    <div id="<?php echo $id; ?>" class="dialog_container hidden">
        <div class="head_panel"><span class="minify"></span><span class="close"></span></div>
        <div>Диалог с пользователем <span class="talkerName"><?php echo $model -> name; ?></span></div>
        <div class="more"><span class="moreSpan">Больше сообщений</span></div>
        <div class="letters">

        </div>
        <div class="form">
            <textarea class="input"></textarea>
            <input class="send" type="submit" value="Отправить"/>
        </div>
    </div>
</div>