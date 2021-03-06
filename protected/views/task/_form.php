<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.07.2016
 * Time: 10:22
 */
/**
 * @type Task $model
 */


Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/underscore.js', CClientScript::POS_BEGIN);
//Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/jquery-ui.min.js', CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/Classes.js', CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/jquery.cookie.js',CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScript("confirm exit",'
$("#goToProject").click(function(){
    return confirm("Все изменения будут потеряны. Вы, действительно, хотите выйти?");
});
',CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerCssFile(Yii::app() -> baseUrl . '/css/taskCreate.css');
Yii::app() -> getClientScript() -> registerCssFile(Yii::app() -> baseUrl . '/css/tree.css');

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
    generateButtons: function (branch){
        if (!branch) {return;}
        if (!branch.parent.parent) {return;}
        branch.editButton = $("<a>",{
            target:\'_blank\',
            "class":"editButton button",
            href: baseUrl + "/task/edit/" + branch.id
        });
        branch.buttonContainer.append(branch.editButton);
        branch.addWordsButton = $("<a>",{
            target:\'_blank\',
            "class":"addWordsButton button",
            href: baseUrl + "/loadKeywords/" + branch.id
        });
        branch.buttonContainer.append(branch.addWordsButton);
        branch.viewButton = $("<a>",{
            target:\'_blank\',
            "class":"viewButton button",
            href: baseUrl + "/task/" + branch.id
        });
        branch.buttonContainer.append(branch.viewButton);
        branch.addDescendantButton = $("<a>",{
            target:\'_blank\',
            "class":"addDescendantButton button",
            href: baseUrl + "/TaskCreate/parent/" + branch.id
        });
        branch.buttonContainer.append(branch.addDescendantButton);

        branch.element.attr(\'data-id\',branch.id);
        branch.textEl.droppable({
            hoverClass:\'over\',
            scope:\'phrase\',
            drop:function(event, ui){
                console.log(ui.draggable);
                var obj = ui.draggable.data("obj");
                $.post(baseUrl + \'/SearchPhrase/move/\'+ ui.draggable.attr(\'data-id\') +\'/to/\'+ branch.id);

                obj.unUse();
                obj.element.remove();
                //location.href = baseUrl + \'/SearchPhrase/move/\'+ ui.draggable.attr(\'data-id\') +\'/to/\'+ branch.id;
            }
        });
        branch.buttonContainer.append(branch.dragButton);
    }
    });
',CClientScript::POS_END);
//$this -> renderPartial('//_navBar');
//Если нет уже созданных фраз, генерируем автонабор
/*if (!$model -> keyphrases) {
    $model->generateKeyPhrases();
}*/
?>
<!--<link rel="stylesheet" href="css/usergen.css"/>
<script src="js/jquery.min.js"></script>
<script src="js/underscore.js"></script>
<script src="js/Classes.js"></script>-->
<script>
    $(document).ready(function(){
        Word.prototype.container = $("#wordsCont");
        Phrase.prototype.container = $("#phrasesCont");
        Phrase.prototype.InitialPhrasesContainer = $("#initialPhrasesCont");
        Phrase.prototype.phraseCountCont = $("#phraseCount");
        <?php
            foreach($model -> keywords as $key) {
                /**
                * @type Keyword $key
                */
                $stems = $key -> giveStems();
                //Нужен только один корень.
                if (count($stems) > 1) {array_splice($stems,1);}
                $stems = json_encode($stems, JSON_PRETTY_PRINT);
                $key -> num = $key -> num ? $key -> num : 0;
                echo "new Word('{$key -> giveShortcut()}',{stems:$stems,num:{$key -> num}});".PHP_EOL;
            }
            foreach ($model -> searchphrases as $sp) {
                /**
                 * @type SearchPhrase $sp
                 */
                $stems = json_encode($sp -> giveStems(),JSON_PRETTY_PRINT);
                $freq = $sp -> phraseFreq;
                echo "new Phrase('$sp->phrase',".json_encode([
                    "stems" => $stems,
                    "initial" => true,
                    "freq" => $freq,
                    "id" => $sp -> id,
                ]).")".PHP_EOL;
                //break;
            }
            foreach ($model -> keyphrases as $kp) {
                echo "new Phrase('$kp->phrase',".json_encode([
                    "stems" => json_encode($kp -> giveStems(),JSON_PRETTY_PRINT),
                    "fromDb" => true,
                    "dbId" => $kp -> id,
                    "strict" => $kp -> direct,
                    "morph" => $kp -> morph,
                    "freq" => $kp -> freq,
                    "tag" => $kp -> tag
                ]).")".PHP_EOL;
            }
        ?>
    });
</script>
<form  method="post">
<div id="phrasesWrapper">
    <div id="phrasesCont">
        <div class="well">
            <input type="text" name="Task[name]" placeholder="Название" value="<?php echo $model -> name; ?>"/>
        </div>
        <div class="well">
            Автор:
            <?php
            UHtml::activeDropDownListChosen2(Task::model(), 'id_author',
                UHtml::listData(User::model() -> author() -> findAll(),'id','username'),
                array('empty_line' => true,'style' => 'width:300px'),array_filter([$model -> id_author]),
                json_encode(array('placeholder' => 'Автор не выбран','allowClear' => true, 'multiple' => false)));
            if ($model -> id_author) {
                Yii::app()->getClientScript()->registerScript('sel_val', '
                $("#Task_id_author").val(' . $model->id_author . ').trigger("change");
            ', CClientScript::POS_READY);
            }
            ?>
        </div>
        <div class="well">
            <?php
                echo UHtml::activeNumberField($model,'min_length',['style' => 'width:40%','placeholder' => 'Минимальная длина текста','step' => 100]);
            ?>
            <?php
            echo UHtml::activeNumberField($model,'max_length',['style' => 'width:40%; margin-left:20px','placeholder' => 'Максимальная длина текста','step' => 100]);
            ?>
        </div>
        <div class="well">
            Шаблон:
            <?php
            UHtml::activeDropDownListChosen2(Task::model(), 'id_pattern',
                UHtml::listData(TaskPattern::model() -> findAll(),'id','name'),
                array('style' => 'width:300px'),array($model -> id_pattern),json_encode(array('placeholder' => 'Шаблон')));
            ?>
        </div>
        <input type="submit" value="В проект" title="Перейти в меню проекта. Все изменения будут сохранены!"/>
        <input type="button" value="Еще фраза" title="Или нажмите Enter во время редактирования любой строки" onClick="new Phrase('',{})"/>

        <input type="button" onClick="Phrase.prototype.completeSet();" value="Дополнить фразы до покрытия" title="Проходит снизу вверх по словам справа. Если слово еще не употреблено нужное количество раз, то ищется самая тяжелая поисковая фраза, содержащая это слово, и переводится в ключевую фразу (появляется ниже, где можно писать фразы вручную). Далее идет следующее слово. Таким образом, будут употреблены все ранее не употребленные слова. Замечу, что эту кнопку можно нажимать даже когда какое-то количество ключевых фраз уже создано. Уже имеющиеся фразы изменены не будут."/>
        <input type="button" onClick="Phrase.prototype.reorderPhrases();" value="Учесть пассажи" title="Работает только для ключевых фраз, которые НЕ помечены замочком (красная иконка замка). Перемтавляет местами слова в ключевых фразах так, чтобы увеличить вес фразы. Под весом фразы понимается сумма частот по всем входящим в нее поисковым фразам с учетом порядка слов! Например, вес фразы 'мрт головного мозга цена' при наличии поисковых фраз 'цена мрт головного мозга', 10; 'цена мрт', 5; 'мрт головного мозга', 20 будет равна 20. Если же поместить слово 'цена' в начало, то вес станет 35. "/>
        <input type="button" onClick="Phrase.prototype.countFreq();" value="Пересчитать частоты" title="Считает веса всех ключевых фраз и ставит их в окошко частоты. Часто запускается автоматически, но можно иногда запускать вручную." />
        <input type="<?php echo $buttons ? 'button' : 'submit'; ?>" name="redirectToShowTask" id="redirectToShowTask" value="Смотреть результат" title="Переход к окну выполнения/контроля задания. Все изменения будут сохранены."/>
        <input type="<?php echo $buttons ? 'button' : 'submit'; ?>" name="redirectToKeywords" id="redirectToKeywords" value="Загрузить данные из KeyCollector" title="Перейти к окну добавления поисковых фраз и ключевых слов. Все изменения будут сохранены."/>
        <div>Всего фраз: <span id="phraseCount"></span></div>
    </div>
    <div id="TreeContainer">

    </div>
    <div id="initialPhrasesCont">

    </div>

</div>
<table id="keywords">
    <thead>
    <tr><td>Слово</td><td>Псевдокорень</td><td>Количество</td><td>Важность</td></tr>
    </thead>
    <tbody id="wordsCont">

    </tbody>
</table>
<div id="rightPaneButtons">
    <input type="button" onClick="Word.prototype.showAll()" value="Показать все слова" />
    <p>Чтобы удалить слово, нажмите Shift+ЛКМ.</p>
    <p>Чтобы скрыть слово, нажмите Ctrl+ЛКМ.</p>
    <p>Чтобы посмотреть фразы, содержащие слово, Alt+ЛКМ.</p>
    <p>Слова можно перетаскивать в инпуты слева.</p>
</div>
</form>