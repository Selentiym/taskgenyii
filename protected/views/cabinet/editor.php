<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 10:50
 */
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/Classes.js',CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/underscore.js',CClientScript::POS_BEGIN);
Yii::app() -> getClientScript() -> registerScript('structure','
    //var treeCont = $("#TreeContainer");
    new TreeStructure("'.addslashes(Yii::app() -> urlManager -> createUrl('task/children')).'",{clickHandler: function(e){
        if (!e) {
            return false;
        } else {
            if (e.shiftKey) {
                this.link.attr("href",baseUrl + "/loadKeywords/parent/"+this.id);
                return false;
            } else if (e.ctrlKey) {
                this.link.attr("href",baseUrl + "/task/"+this.id);
                return false;
            }
            e.preventDefault();
            return true;
        }
    },
    toHref: function(){
        if (this.id) {
            return baseUrl + "/loadKeywords/parent/"+this.id;
        }
    }
    });
',CClientScript::POS_READY);
?>
ЛКМ => закрыть/открыть список одветок<br/>
Ctrl + ЛКМ => открыть в новой вкладке таблицу ключевых слов<br/>
Shift + ЛКМ => открыть в новом окне окошко создания нового ТЗ в качестве дочернего к выбранной ветке дерева<br/>
<div id="TreeContainer">

</div>
