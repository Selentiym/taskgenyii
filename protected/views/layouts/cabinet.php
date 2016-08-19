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
Yii::app() -> getClientScript() -> registerScript('addNewDialog',"$('.dialogCreator').click(addNewDialogFromClick);", CClientScript::POS_READY);
?>
<div class="anchor">
    <div id="topPanel">
        <div id="topPanelInner">
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
            <div id="nav">
            <?php
            Yii::app() -> controller -> renderPartial('//_navBar');
            ?>
            </div>
        </div>
    </div>
</div>
<?php
echo $content;
?>
