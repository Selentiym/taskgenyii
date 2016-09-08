<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 10:50
 */
//$this -> renderPartial('//_navBar');
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
    //new ControlButton("selected","giveSelected",function(el){if (el.ticked) {alert("el.name");}},tree);
    new ControlButton("","edit",function(el){location.href = baseUrl + "/task/edit/" + el.id; return true;},tree, {},ControlButton.prototype.actionForOneCountChecks);
    new ControlButton("","keys",function(el){location.href = baseUrl + "/cabinet/loadKeywords/" + el.id; return true;},tree, {},ControlButton.prototype.actionForOneCountChecks);
    new ControlButton("","look",function(el){location.href = baseUrl + "/task/" + el.id; return true;},tree,{}, ControlButton.prototype.actionForOneCountChecks);
    //new ControlButton("","plus",function(el){location.href = baseUrl + "/TaskCreate/parent/" + el.id; return true;},tree);
    new ControlButton("","plus",function(el){$.post(
        baseUrl + "/Task/createFast/" + el.id,
        {name:prompt("Введите имя объекта","Новая статья")}, null,"json"
    ); return true;},tree);
    new ControlButton("&#9745;","font20",function(el){el.iterateOverDescendants(function(child){child.setSelected(true);
    child.parent.childrenContainer.show(500);

    },true);},tree,{title:"Выделить потомков"}, true);
    new ControlButton("&#9746;","font20",function(el){el.iterateOverSelfAndDescendants(function(child){child.setSelected(false);});},tree,{title:"Снять выделение с потомков"}, true);
    new ControlButton("","delete",function(el, event, collection){
        var toDel = [];
        var yesToAll;
        if (collection.length > 1) {
            yesToAll = !confirm("Вы собираетесь удалить "+collection.length+" заданий. Перечислить их по очреди?");
            _.map(collection, function(elem){
            var toDelFlag = yesToAll;
            if (!toDelFlag) {
                toDelFlag = confirm("Удалить задание: "+elem.name+"?");
            }
            if (toDelFlag) {
                toDel.push(elem.id);
            }
        });
        } else {
            if (confirm("Удалить задание: "+el.name+"?")) {
                toDel = [el.id];
            }
        }
        console.log(toDel);
        $.post(baseUrl+"/task/deleteGroup",{ids:toDel},function(){},"JSON").done(function(data){
            alert(data);
            location.reload();
        });
        //Не продолжаем потом прбегать по элементам
        return true;
    },tree,{title:"Снять выделение с потомков"}, true);
',CClientScript::POS_READY);
?>
<div id="controlPanel">
</div>
<div id="TreeContainer">

</div>

<?php //$this -> renderPartial('//cabinet/dialog', array('model' => User::model() -> findByPk(3))); ?>