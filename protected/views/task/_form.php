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
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/jquery-ui.min.js', CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerScriptFile(Yii::app() -> baseUrl . '/js/Classes.js', CClientScript::POS_END);
Yii::app() -> getClientScript() -> registerCssFile(Yii::app() -> baseUrl . '/css/taskCreate.css');

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
                $freq = $sp -> directFreq;
                echo "new Phrase('$sp->phrase',{stems:$stems, initial:true, freq:'$freq'})".PHP_EOL;
                //break;
            }
            foreach ($model -> keyphrases as $kp) {
                $stems = json_encode($kp -> giveStems(),JSON_PRETTY_PRINT);
                $id = $kp -> id;
                echo "new Phrase('$kp->phrase',{stems:$stems, fromDb: true, dbId:'$id'})".PHP_EOL;
            }
        ?>
    });
</script>
<div id="phrasesWrapper">
    Чтобы посмотреть список фраз, в которых используется слово, нажмите Alt + ЛКМ на строке со словом в правой части<br/>
    Чтобы удалить слово совсем, нажмите Shift + ЛКМ на слове<br/>
    Чтобы скрыть слово, которое не нужно, но может потом пригодиться, нажмите Ctrl + ЛКМ<br/>
    Слова из правой части можно перетаскивать в форму слева простым удерживанием ЛКМ<br/>
    <form method="post" id="phrasesCont">
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
            Шаблон:
            <?php
            UHtml::activeDropDownListChosen2(Task::model(), 'id_pattern',
                UHtml::listData(TaskPattern::model() -> findAll(),'id','name'),
                array('style' => 'width:300px'),array($model -> id_pattern),json_encode(array('placeholder' => 'Шаблон')));
            ?>
        </div>
        <input type="submit" value="Сохранить"/>
        <input type="button" value="Еще фраза" title="Или нажмите Enter во время редактирования любой строки" onClick="new Phrase('',{})"/>
        <input type="button" onClick="Word.prototype.showAll()" value="Показать все слова" />
        <input type="button" onClick="Phrase.prototype.completeSet();" value="Дополнить фразы до покрытия" />
    </form>
</div>
<table id="keywords">
    <thead>
    <tr><td>Слово</td><td>Псевдокорень</td><td>Количество</td><td>Важность</td></tr>
    </thead>
    <tbody id="wordsCont">

    </tbody>
</table>

