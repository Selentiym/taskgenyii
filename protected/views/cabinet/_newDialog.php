<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 18.08.2016
 * Time: 11:52
 */
UHtml::activeDropDownListChosen2(Task::model(), 'id_author',
    UHtml::listData(User::model() -> findAll(),'username','name'),
    array('empty_line' => true,'style' => 'width:200px'),[],
    json_encode(array('placeholder' => 'Открыть диалог','allowClear' => true, 'multiple' => false)));
Yii::app() -> getClientScript() -> registerScript('dialogSelect','
    var select2 = $("#Task_id_author");
    select2.on("select2:select", function (e) { addNewDialog(e.params.data.id); });
',CClientScript::POS_READY);
?>