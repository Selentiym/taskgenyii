<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 10:11
 */
$this -> renderPartial('//layouts/commonScripts');
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl.'/js/dialog.js', CClientScript::POS_BEGIN);
Yii::app() -> getClientScript() -> registerScript('saveDialogCont','Dialog.prototype.container = $("#dialogContainer");', CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScript('addNewDialog',"$('.dialogCreator').click(addNewDialog);", CClientScript::POS_READY);
?>
<div id="dialogContainer">
    <?php
    $this->renderPartial('//cabinet/_notifications');
    foreach(User::logged() -> newLetters as $letter){
        if ($letter -> id_sender != 0) {
            $this->renderPartial('//cabinet/dialog', ['model' => $letter->sender]);
        }
    };
    ?>
</div>

<?php
echo $content;
?>
