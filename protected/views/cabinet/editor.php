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
    new ControlButton("","delete_keys",function(el){$.post(baseUrl+"/Task/deleteKeys/"+el.id).done(function(){location.reload();});},tree,{},
    function (coll) {if (coll.length) {return confirm("Вы собираетесь удалить поисковые фразы и ключевые слова у " + coll.length + " заданий. Это действие необратимо. Продолжить все равно?");} else {return false;}});
    //new ControlButton("","plus",function(el){location.href = baseUrl + "/TaskCreate/parent/" + el.id; return true;},tree);
    new ControlButton("","plus",function(el){$.post(
        baseUrl + "/Task/createFast/" + el.id,
        {name:"Новая статья"}, null,"json"
    ).done(function(data){
        if (data.success) {
            el.createChild(data.dump);
        } else {
            alert("Ошибка при создании!");
        }
    }); return true;},tree);
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
        function sendDeleteRequest(toDel, forced){
            if (forced) {
                forced = 1;
            } else {
                forced = 0;
            }
            $.post(baseUrl+"/task/deleteGroup",{ids:toDel, forced: forced},function(){},"JSON").done(function(data){
                alert(data.commonMess);
                console.log(data);
                var toDelSecond = [];
                if (data.forcedDel) {
                    _.each(data.forcedDel, function(elem){
                        if (confirm(elem.mes)) {
                            toDelSecond.push(elem.id);
                        }
                    });
                }
                if (toDelSecond.length) {
                    sendDeleteRequest(toDelSecond, true);
                }
                if (data.reload) {
                    location.reload();
                }
            });
        }
        sendDeleteRequest(toDel);
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