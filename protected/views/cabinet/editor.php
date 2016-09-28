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
    generateButtons: addButtons,
    generatePanel: genControlPanel
    });
',CClientScript::POS_READY);
?>
<div id="controlPanel">
    <?php
        $authors = UHtml::listData(User::model() -> author() -> findAll(),"id","name");
        echo UHtml::dropDownList("author",null,$authors,['id'=>"authorsList","style" => "width:200px"] );
    ?>
</div>
<div id="TreeContainer">

</div>
<div>
<?php echo CHtml::link("Создать автора",Yii::app() -> createUrl("author/Create"), ['class' => 'buttonText']); ?>
<?php UHtml::activeDropDownListChosen2(Author::model(),'id',UHtml::listData(User::model() -> author() -> findAll(),"id","name"),['id'=>'changeAuthor','empty_line' => true],[],json_encode(['placeholder' => 'Выберите автора']));
Yii::app() -> getClientScript() -> registerScript('changeAuthorPage',"
    $('#changeAuthor').on('select2:select', function(e){
        location.href = baseUrl + '/author/Stat/' +e.params.data.id;
    });
",CClientScript::POS_READY);
?>
</div>
<div>
<?php echo CHtml::link("Создать шаблон",Yii::app() -> createUrl("cabinet/PatternCreate"), ['class' => 'buttonText']); ?>
<?php UHtml::activeDropDownListChosen2(Pattern::model(),'id',UHtml::listData(Pattern::model() -> findAll(),"id","name"),['id'=>'changePattern','empty_line' => true],[],json_encode(['placeholder' => 'Выберите шаблон']));
Yii::app() -> getClientScript() -> registerScript('changePatternPage',"
    $('#changePattern').on('select2:select', function(e){
        location.href = baseUrl + '/cabinet/patternEdit/' +e.params.data.id;
    });
",CClientScript::POS_READY);
?>
</div>
<?php //$this -> renderPartial('//cabinet/dialog', array('model' => User::model() -> findByPk(3))); ?>