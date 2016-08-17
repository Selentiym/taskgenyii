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
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/jquery.cookie.js',CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/underscore.js',CClientScript::POS_BEGIN);
Yii::app() -> getClientScript() -> registerScript('structure','
    //var treeCont = $("#TreeContainer");
    var tree = new TreeStructure("'.addslashes(Yii::app() -> urlManager -> createUrl('task/children')).'",{clickHandler: function(e){
        if (!e) {
            return false;
        } else {
            /*if ((e.ctrlKey)&&(e.shiftKey)) {
                this.link.attr("href",baseUrl + "/task/edit/"+this.id);
                return false;
            } else if ((e.shiftKey)) {
                this.link.attr("href",baseUrl + "/TaskCreate/parent/"+this.id);
                return false;
            } else if (e.ctrlKey) {
                this.link.attr("href",baseUrl + "/loadKeywords/"+this.id);
                return false;
            }*/
            e.preventDefault();
            //return true;
            return false;
        }
    },
    toHref: function(){
        if (this.id) {
            return baseUrl + "/loadKeywords/"+this.id;
        }
    },
    generateButtons: addButtons
    });
    //new ControlButton("selected","giveSelected",function(el){return false;},tree);
    new ControlButton("","edit",function(el){location.href = baseUrl + "/task/edit/" + el.id; return true;},tree);
    new ControlButton("","keys",function(el){location.href = baseUrl + "/cabinet/loadKeywords/" + el.id; return true;},tree);
    new ControlButton("","look",function(el){location.href = baseUrl + "/task/" + el.id; return true;},tree);
    new ControlButton("","plus",function(el){location.href = baseUrl + "/task/" + el.id; return true;},tree);
    new ControlButton("","selectDescendants",function(el){el.iterateOverDescendants(function(child){child.setSelected(true);});},tree,{title:"Выделить потомков"});
',CClientScript::POS_READY);
?>
<div id="controlPanel">
</div>
<div id="TreeContainer">

</div>

<?php //$this -> renderPartial('//cabinet/dialog', array('model' => User::model() -> findByPk(3))); ?>