<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 10:50
 */
$this -> renderPartial('//_navBar');
Yii::app() -> getClientScript() -> registerCss('asd','.square {display:block; width:20px; height:20px; background:#123; margin:10px;}');
Yii::app() -> getClientScript() -> registerCssFile(Yii::app() -> baseUrl . '/css/tree.css');
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/Classes.js',CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/underscore.js',CClientScript::POS_BEGIN);
Yii::app() -> getClientScript() -> registerScript('structure','
    //var treeCont = $("#TreeContainer");
    new TreeStructure("'.addslashes(Yii::app() -> urlManager -> createUrl('task/children')).'",{clickHandler: function(e){
        if (!e) {
            return false;
        } else {
            if ((e.ctrlKey)&&(e.shiftKey)) {
                this.link.attr("href",baseUrl + "/task/edit/"+this.id);
                return false;
            } else if ((e.shiftKey)) {
                this.link.attr("href",baseUrl + "/TaskCreate/parent/"+this.id);
                return false;
            } else if (e.ctrlKey) {
                this.link.attr("href",baseUrl + "/loadKeywords/"+this.id);
                return false;
            }
            e.preventDefault();
            return true;
        }
    },
    toHref: function(){
        if (this.id) {
            return baseUrl + "/loadKeywords/"+this.id;
        }
    },
    generateButtons: addButtons
    });
',CClientScript::POS_READY);
?>
ЛКМ => закрыть/открыть список подветок<br/>
Shift + Ctrl + ЛКМ => открыть в новой вкладке таблицу ключевых слов<br/>
Shift + ЛКМ => открыть в новом окне окошко создания нового ТЗ в качестве дочернего к выбранной ветке дерева<br/>
Ctrl + ЛКМ => добавить поисковые фразы к заданию из KeyCollector'а
<div id="check">
    <div class="square"></div>
    <div class="square"></div>
</div>
<div id="TreeContainer">

</div>

<?php //$this -> renderPartial('//cabinet/dialog', array('model' => User::model() -> findByPk(3))); ?>