<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.08.2016
 * Time: 10:58
 */
/**
 * @type User $user
 */
if (!$user) {
    $user = User::logged();
}
$id = $user -> username;

Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl. '/js/dialog.js',CClientScript::POS_END);
Yii::app() -> getClientScript() -> registercssFile(Yii::app() -> baseUrl. '/css/dialog.css');

Yii::app() -> getClientScript() -> registerScript('dialog',
"new Dialog($('#$id'),".$user -> id.",0,'".date('Y-m-d H:i:s')."');",CClientScript::POS_READY);
$this -> renderPartial('//cabinet/_newDialog');
?>
<div id="<?php echo $id; ?>_shortcut" class="dialog_shortcut"></div>
<div class="toDrag" style="z-index:5;">
    <div id="<?php echo $id; ?>" class="dialog_container hidden">
        <div><span class="talkerName">Уведомления</span>:</div>
        <div class="more"><span class="moreSpan">Больше сообщений</span></div>
        <div class="letters">

        </div>
    </div>
</div>