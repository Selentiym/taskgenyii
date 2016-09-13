/**
 * Created by user on 06.07.2016.
 * @require jQuery
 * @require Underscore.js
 * @require jQuery-ui.js
 * @require jquery.cookie.js
 */
var body = $("body");
var phrasesCount = {};
Lexical.prototype.wordsPool = [];
Lexical.prototype.phrasesPool = [];
Lexical.prototype.initialPhrasesPool = [];
Word.prototype.showAll = function(){
    _.each(Lexical.prototype.wordsPool, function (word) {
        word.show();
    });
};
/**
 * Функция пробегает по всему словарю в возрастающем порядке, выцепляя наиболее популярные
 * фразы, содаржащие это слово
 */
Phrase.prototype.completeSet = function(){
    //Пробегаем по всем словам. порядок по .num должен быть по возрастанию
    _.each(_.sortBy(Lexical.prototype.wordsPool, 'num'), function(el){
        //Если слово еще нужно использовать, то пытаемся найти для него фразу.
        if (el.counter > 0) {
            var toSearch = el.getInitialPhrases();
            if (toSearch.length > 0) {
                var maxNum = -1;
                var toUse = false;
                _.each(toSearch, function(phr){
                    if (phr.freq > maxNum) {
                        maxNum = phr.freq;
                        toUse = phr;
                    }
                });
                if (toUse) {
                    new Phrase(toUse.text, {fromDb: false, stems: toUse.stems, freq:toUse.freq });
                }
            }
        }
    });
};
function Lexical(text, param) {
    var me = {};
    //Чтобы лишний раз не дергать сервер, анализировать будем только если флаг false
    me.analyzed = false;
    me.text = text;
    me.onAfterAnalyze = function () { return; };
    me.analyze = function(){
        if (!me.analyzed) {
            $.ajax({
                url: baseUrl + '/stem',
                method: 'get',
                dataType: 'json',
                data: {
                    text: me.text
                }
            }).done(function (data) {
                me.stems = data.stems;
                console.log(data);
                me.analyzed = true;
                me.onAfterAnalyze();
            });
        } else {
            me.onAfterAnalyze();
        }
    };
    return me;
}
/*function InitialPhrase(text, nums){
    var me = new Lexical();
    Lexical.prototype.initialPhrasesPool.push(me);
    me.analyze();
    /**
     * Проверяет, есть ли слово в данной фразе
     * @param word string|Word
     * @param strict bool

    me.hasWord = function (word, strict) {
        if (strict === undefined) {
            strict = false;
        }
        //Если нам передан объект не унаследованный от Lexical
        if (word.text === undefined) {
            word = new Word(word,{});
        }
        if (strict) {
            return (me.text.indexOf(word.text) > -1);
        } else {
            return (me.stems.indexOf(word.stem) > -1);
        }
    };
    return me;
}*/
var popups = [];
$(document).click(function(){
    if (popups.length) {
        var el;
        _.each(popups, function(el){ el.remove();});
        popups = [];
    }
});
var baseFormInputName = 'Task';
function Phrase(text, param){
    var me = new Lexical(text, param);
    if (param.id) {
        me.id = param.id;
    }
    //Сохраняем частотность
    me.freq = param.freq;
    if (param.stems instanceof Array) {
        me.analyzed = true;
        me.stems = param.stems;
    }
    //Если фраза исходная, то есть поисковая и подается на вход, то
    //в будущем не нужно считать ее вхождения
    me.initial = param.initial === true;
    //Изначальные фразы не должны отобржаться на странице.
    if (!me.initial) {
        if (!param.strict) {
            param.strict = null;

            if (!param.morph) {
                param.morph = 1;
            }
        }
        if (!param.morph) {
            param.morph = null;
        }
        me.element = new LinkedDom('div', {'class': 'phrase'}, me);
        me.inputEl = $('<input>', {
            type: 'text',
            name: baseFormInputName + '[phrases][text][]',
            placeholder: 'Введите фразу',
            value: text
        });
        me.element.append($('<input>', {
            type: 'text',
            name: baseFormInputName + '[phrases][strict][]',
            placeholder: 'Прямое',
            value: param.strict,
            "class": "strict"
        }));
        me.element.append($('<input>', {
            type: 'text',
            name: baseFormInputName + '[phrases][morph][]',
            placeholder: 'Морфология',
            value: param.morph,
            "class": "morph"
        }));
        me.element.append(me.inputEl);
        me.freqEl = $("<input>",{
            "class":"freq",
            name: baseFormInputName + '[phrases][freq][]',
            value:param.freq,
            placeholder:"Частотность"
        });
        me.element.append(me.freqEl);
        me.setFreq = function(val){
            me.freq = val;
            me.freqEl.val(val);
        };
        me.element.append($('<span>',{
            "class": "deletePhraseInput"
        }).click(function(){
            me.removePhrase();
        }));
        //Позволяем инпуту ловить ключевые слова
        me.inputEl.droppable({
            drop: function (e, ui) {
                var word = ui.helper.data("obj");
                me.inputEl.val(me.inputEl.val() + ' ' + word.text);
                me.refresh();
            }
        });
        me.changeElement = $('<input>',{
            type:'hidden',
            name:baseFormInputName + '[phrases][changed][]'
        });
        me.element.append(me.changeElement);
        me.linked = [];
        me.highlight = function(phr){
            me.linked.push(phr);
            me.element.addClass("highlighted");
        };
        me.clearHighlight = function(){
            if (me.linked.length) {
                _.each(me.linked, function(el){
                    el.removeHightlight(me);
                });
            }
            me.linked = [];
            me.element.removeClass("highlighted");
        };
        me.removeHightlight = function(phr){
            var ind = me.linked.indexOf(phr);
            if (ind != -1) {
                me.linked.splice(ind, 1);
            }
            if (!me.linked.length){
                me.element.removeClass("highlighted");
            }
        };
        //Контейнер для элеентов данного типа должен быть задан в прототипе.
        Phrase.prototype.container.append(me.element);
    } else {
        me.element = new LinkedDom('div',{
            "class":"initialPhrase"
        }, me);
        me.element.attr('data-id',me.id);
        me.element.append(me.text);
        Phrase.prototype.InitialPhrasesContainer.append(me.element);
        me.element.dblclick(function(){
            new Phrase(me.text, {fromDb: false, stems: me.stems });
        });
        me.element.draggable({
            helper:"clone",
            scope:"phrase"
        });
    }
    /**
     * информация по БД
     */
    //Тут будет храниться информация, которая связана с хранением объекта в БД
    //Например, айдишник, флаг из БД ли выгружена фраза.
    me.db = {};
    if (param.dbId) {
        me.db.id = param.dbId;
    }
    me.setDbChanged = function(val){
        if (!me.initial) {
            me.changeElement.val(val);
        }
        me.db.changed = val;
    };
    if ((param.fromDb)&&(param.dbId)) {
        me.setDbChanged(0);
    } else {
        me.setDbChanged(1);
    }

    /**
     * Если хоть что-то поменялось, то в дальнейшем нужно будет удалить старую
     * фразу из БД, добавить новую
     */
    me.somethingChanged = function(){
        if ((me.db.changed == 0)&&(me.db.id)) {
            //Добавляем в список удаляемых.
            me.element.append($('<input>',{
                type:'hidden',
                value:me.db.id,
                name:baseFormInputName + '[phrases][toDel][]'
            }));
        }
        me.setDbChanged(1);
    };
    /**
     * Конец информации по БД
     */
    Lexical.prototype.phrasesPool.push(me);
    /**
     * Генерирует вывод небольшого окошка с текстом фразы и частотностью
     */
    me.shortCut = function(){
        var temp = $('<div>',{
            "class":"phraseShortcut"
        }).html(me.text + ((me.freq !== undefined) ? (', ' + me.freq) : '' ));
        if (me.initial) {
            temp.addClass('initial');
        }
        return temp;
    };
    //Если пользователь изменил фразу
    me.refresh = function() {

        me.somethingChanged();
        me.clearHighlight();
        if (me.used) {
            _.each(me.words, function(word) {
                word.getUnused(me, me.initial);
            });
        }

        me.stems = [];
        //me.text = me.
        me.found = false;
        me.used = false;
        me.analyzed = false;
        me.text = me.inputEl.val();

        me.analyze();
    };
    /**
     * Будет хранить ссылки на объекты всех
     * присутсвующих в фразе ключевых слов
     * @type Word[]
     */
    me.words = [];
    //false => need to find words
    me.found = false;
    //Находим все ключевики
    me.findWords = function () {
        if (me.found) {return;}
        me.words = [];
        _.each(me.stems,function(stem){
            //Пробегаем все созданные слова
            _.each(Lexical.prototype.wordsPool, function(word){
                if (word.stem == stem) {
                    me.words.push(word);
                }
            });
        });
        me.found = true;
    };
    /**
     * Считаем фразу использованной, то есть вычитаем ее слова.
     */
        //Хранит информацию о том, были ли фраза использована.
        //Если не была, то потом не нужно прибавлять единицу к словам при ее удалении
    me.used = false;
    me.use = function(){
        if (!me.used) {
            me.findWords();
            //Используем все слова.
            _.each(me.words, function (word) {
                word.getUsed(me, me.initial);
            });
            me.used = true;
        }
    };
    me.unUse = function(){
        if (me.used) {
            _.each(me.words, function(word) {
                if (word) {
                    word.getUnused(me, me.initial);
                }
            });
            me.used = false;
        }
    };
    /**
     * Проверяет, есть ли слово в данной фразе
     * @param word string|Word
     * @param strict bool
     */
    me.hasWord = function (word, strict) {
        if (strict === undefined) {
            strict = false;
        }
        //Если нам передан объект не унаследованный от Lexical
        if (word.text === undefined) {
            word = new Word(word,{});
        }
        if (strict) {
            return (me.text.indexOf(word.text) > -1);
        } else {
            return (me.stems.indexOf(word.stem) > -1);
        }
    };

    me.onAfterAnalyze = function(){
        //Проверяем, нет ли такой же фразы, но занятой.
        _.each(Lexical.prototype.phrasesPool,function(phrase, key){

            if ((phrase.analyzed)&&(!phrase.initial)&&(phrase)&&(phrase.stems.length)&&(me.stems.length)&&(me != phrase)) {
                console.log(phrase);
                var shorter = phrase;
                var longer = me;
                if (phrase.stems.length > me.stems.length) {
                    shorter = me;
                    longer = phrase;
                }
                var intersect = _.intersection(longer.stems, shorter.stems);
                if (intersect.length == shorter.stems.length) {
                    longer.highlight(shorter);
                    shorter.highlight(longer);
                    //alert('Фраза "' + shorter.text + '" морфологически включена в фразу "' + longer.text+'"');
                }
            }
        });
        //Нам же интересно, чтобы фраза была использована
        me.use();
    };
    me.analyze();
    if (!me.initial) {
        me.element.change(function (e) {
            me.refresh();
        });
        me.element.keypress(function (e) {

            if (e.which == 13) {
                var temp = new Phrase('', {});
                e.preventDefault();
                //e.preventBubble();

                //temp.element.get().focus();

            }
        });
    }
    /**
     * Удаляем фразу. Просто удалить можно только если она не имеет праобраза в БД.
     * Если имеет, то нужно передать при сабмите, что нужно эту фразу удалить.
     */
    me.removePhrase = function(){
        me.inputEl.val("");
        me.refresh();
        if (me.db.id) {
            me.somethingChanged();
            me.element.hide();
        } else {
            me.element.remove();
        }
        Lexical.prototype.phrasesPool.splice(Lexical.prototype.phrasesPool.indexOf(me),1);
        Phrase.prototype.countPhrases();
    };
    Phrase.prototype.countPhrases();
    return me;
}
Phrase.prototype.countPhrases = function() {
    phrasesCount = _.countBy(Lexical.prototype.phrasesPool, function (phrase) {
        return phrase.initial ? 'searchphrase' : 'keyphrase';
    });
    Phrase.prototype.phraseCountCont.html(phrasesCount.keyphrase);
};
function Word(text,param) {
    var me = new Lexical(text,param);
    me.num = param.num;
    //Показывает, был ли выделен массив изначальных фраз
    me.initialSearched = false;
    me.counterCell = $('<td>',{"class":"counter"});
    me.stemCell = $('<td>',{"class":"stem"});
    me.textEl = $('<span>',{
        "class":"keywordText",
        css:{
            display:"inline-block"
        }
    }).html(text);
    me.numCell = $('<td>').html(me.num);
    me.element = new LinkedDom('tr',{
        "class":"word"
    },me)
        .append($('<td>', {"class":"fulltext"})
            .append(me.textEl))
        .append(me.stemCell)
        .append(me.counterCell)
        .append(me.numCell);
    //Добавляем возможность скрыть слово
    me.hidden = false;
    me.hide = function() {
        if (!me.hidden) {
            me.element.hide(500);
            me.hidden = true;
        }
    };
    me.show = function () {
        if (me.hidden) {
            me.element.show(500);
            me.hidden = false;
        }
    };
    me.delete = function () {
        me.element.remove();
        Lexical.prototype.wordsPool.splice(Lexical.prototype.wordsPool.indexOf(me),1);
        me = {};
        //console.log(Lexical.prototype.wordsPool);
    };
    me.showPopup = function(top,left){
        var popupEl = $('<div>',{
            "class":"wordPopup",
            css:{
                position:"fixed",
                top:top+"px",
                left:left+"px"
            }
        });
        _.each(me.phrases, function(p){
            popupEl.append(p.shortCut());
        });
        /*popupEl.click(function(){
            $(this).remove();
        });*/
        console.log(popupEl);
        $(document.body).append(popupEl);
        popups.push(popupEl);
    };
    me.element.click(function(e){
        if (e.ctrlKey) {
            me.hide();
        }
        if (e.shiftKey) {
            me.delete();
        }
        if (e.altKey) {
            console.log('alt');
            me.showPopup(e.clientY, e.clientX);
            e.stopPropagation();
        }
    });
    //Добавляем возможность перетаскивать элементы
    me.textEl.draggable({
        helper: function(){
            var el = $('<span>');
            el.html(me.text);
            el.data("obj", me);
            return el;
        },
        start: function(){
            //alert('start');
        }
    });
    //Контейнер для элеентов данного типа должен быть задан в прототипе.
    Word.prototype.container.append(me.element);
    /**
     * Будет содержать массив фраз, использующих данное слово
     * @type Phrase
     */
    me.phrases = [];
    /**
     * Меняем счетчик
     */
    me.setCounter = function(newVal) {
        me.counter = newVal;
        var className = '';
        if (newVal > 0) {
            className = 'someLeft';
        } else {
            className = 'ranOut';
        }
        //me.element.detach().prependTo(Word.prototype.container);
        me.counterCell.html($('<span>',{"class":className}).append(newVal));
    };
    //Если не задано количество повторений слова, то считаем, что нужно всего одно.
    if (!param.counter) {
        param.counter = 1;
    }
    //задаем изначальное значение счетчика
    me.setCounter(param.counter);
    //Добавляем слово в массив всех ключевиков
    Lexical.prototype.wordsPool.push(me);
    me.onAfterAnalyze = function() {
        me.stem = me.stems[0];
        me.stemCell.html(me.stem);
    };
    /**
     * Задействуем слово в фразе.
     * @param phrase
     * @param noChangeCounter
     */
    me.getUsed = function(phrase, noChangeCounter) {
        me.phrases.push(phrase);
        if (!noChangeCounter) {
            me.setCounter(me.counter - 1);
        }
    };
    me.getUnused = function(phrase, noChangeCounter){
        console.log(me);
        var ind = me.phrases.indexOf(phrase);
        me.phrases.splice(ind, 1);
        if (!noChangeCounter) {
            me.setCounter(me.counter + 1);
        } else {
            //Если была отключена поисковая фраза, не ключевая,
            // то уменьшить нужно счетчик важности.
            me.num --;
            if (me.num < 1) {
                me.delete();
            }
            if (me.numCell) {
                me.numCell.html(me.num);
            }
        }
    };
    //Если снаружи задан массив корней слова (должен иметь один элемент)
    if (param.stems) {
        me.stems = param.stems;
        me.onAfterAnalyze();
    }
    //Выдает массив поисковых фраз
    me.getInitialPhrases = function(){
        //Если поиск произведен, просто выдаем
        if (!me.initialSearched) {
            me.initialPhrases = [];
            //Иначе ищем
            _.each(me.phrases, function(phr){
                if (phr.initial) {
                    me.initialPhrases.push(phr);
                }
            });
            me.initialSearched = true;
        }
        return me.initialPhrases;
    };
    return me;
}
function LinkedDom(node, attrs, linkTo){
    var me = $('<'+node+'>',attrs);
    me.data('obj',linkTo);
    me.obj = linkTo;
    return me;
}

var loadingImage = $('<img>', {
    src: baseUrl + '/images/loading.gif',
    css:{
        width:"20px",
        height:"20px"
    }
});
function TreeBranch(parent, param){
    var me = {};
    if (!(param instanceof Object)) { return; }
    //console.log(param);
    //Сохраняем свой id, иначе не будет детей
    me.id = param.id;
    //Понадобится где-нибудь
    me.name = param.name;
    //Хранится специфичная информация, изменяемая в зависимости от типа отображаемого объекта.
    me.extra = param.extra;
    //console.log(me);
    if (!me.extra) {
        me.extra = {};
        //alert('no extra');
    }
    //Сохраняем родителя, иначе не будет отображения на странице
    //Родителя полностью рекурсивно копируем, потом понадобится
    me.parent = $.extend({},parent);
    //Детей пока нет до первого нажатия
    me.children = [];
    //Детей пока не искали
    me.searched = false;

    //Таким образом сохраняем всевозможные атрибуты, передаваемые от родителя к детям
    //Среди них url, method и тд
    //При этом все новые методы/атрибуты должны замениться на дочерние
    //me = $.extend(parent, me);
    me.tree = parent.tree;
    me.url = parent.url;
    me.toHref = parent.toHref;
    me.childFunc = parent.childFunc;
    me.generateButtons = parent.generateButtons;
    me.method = parent.method;
    me.clickHandler = parent.clickHandler;

    /**
     *@todo вынести как-то в отдельную функцию, передаваемую в качестве параметров в будущем
     */
    //Элемент, отображающий ветку дерева
    me.element = $('<li>',{
        "class":"treeBranch"
    });
    me.expandEl = $('<span>',{
        "class":"expand"
    });
    if (me.extra.hasChildren) {
        me.expandEl.addClass('hasChildren');
        me.expandEl.click(function(){
            me.toggle();
        });
    }
    me.element.append(me.expandEl);
    //В нем содержится название ветки (этот же элемент будет отвечать за выделение)
    me.textEl = $('<div>',{
        "class":"branchName"
    });
    me.link = $('<span>',{href: me.toHref()});
    toEdit(me.link,baseUrl + '/task/rename/'+me.id);
    me.textEl.html(me.link.append(param.name));
    me.textEl.click(function(e){
        //Если нажат shift
        if ((e.shiftKey)&&(TreeBranch.prototype.lastSelected)) {
            var indLast = me.parent.children.indexOf(TreeBranch.prototype.lastSelected);
            if (indLast == -1) {
                return;
            }
            var indCur = me.parent.children.indexOf(me);
            if (indCur > indLast) {
                var sv = indLast;
                indLast = indCur;
                indCur = sv;
            }
            for (var i = indCur; i <= indLast; i++) {
                me.parent.children[i].setSelected(true);
            }
            e.preventDefault();
            return;
        }
        if (!e.ctrlKey) {
            me.tree.unselectAll();
        }
        me.toggleSelected();
    });
    /*me.textEl.click(function(e){
        if (me.clickHandler(e)){
            me.toggle();
        }
    });*/
    //И элемент с детьми
    me.element.append(me.textEl);
    me.buttonContainer = $('<span>',{'class':'buttonContainer'});
    if (typeof me.generateButtons == 'function') {
        me.generateButtons(me);
    }
    me.element.append(me.buttonContainer);
    me.childrenContainer = $('<ul>',{
        "class":"branchChildren",
        css:{
            display:"none"
        }
    });
    me.element.append(me.childrenContainer);

    //Присваиваем элемент контейнеру
    me.parent.childrenContainer.append(me.element);
    me.iterateOverChildren = function(callback, obtainChildren){
        if (typeof callback == 'function') {
            if (me.children.length) {
                _.each(me.children, callback);
            } else {
                if ((obtainChildren)&&(!me.searched)&&(me.extra.hasChildren)) {
                    me.getChildren(null, callback);
                }
            }
        }
    };
    me.iterateOverDescendants = function (callback, obtainChildren) {
        if (typeof callback == 'function') {
            me.iterateOverChildren(function (a) {
                callback(a);
                //console.log(a);
                //console.log(obtainChildren);
                a.iterateOverDescendants(callback, obtainChildren);
            }, obtainChildren);
        }
    };
    /*
    me.iterateOverDescendants = function(callback, obtainChildren){
        if ((typeof callback == 'function')&&(me.children)) {
            _.each(me.children, function(a, b, c){
                callback(a, b, c);
                a.iterateOverDescendants(callback);
            });
        }
    };*/
    me.iterateOverSelfAndDescendants = function(callback, obtainChildren){
        if (typeof callback == 'function') {
            callback(me);
            me.iterateOverDescendants(callback);
        }
    };
    /**
     * Отвечает за создание детей. Обращается на сервер и получает своих потомков,
     * затем инициализирует их
     */
    //Вынесено для использования в дальнейшем
    //вне рамок массового обращения на сервер.
    me.createChild = function(el){
        var child = me.childFunc(me, el);
        me.children.push(child);
        if (me.tree.expandedIdsInitial.indexOf(child.id) != -1) {
            child.toggle();
        }
        if (typeof callback == 'function') {
            callback(child);
        }
        return child;
    };
    me.getChildren = function(noExpandedChange, callback){
        me.childrenContainer.toggle(noExpandedChange);
        me.childrenContainer.html(loadingImage.clone());
        $.ajax({
            url: me.url,
            //method:"POST",
            dataType:"json",
            //method:me.method,
            data:{
                id: me.id,
                param: param
            }
        }).done(function (data) {
            me.childrenContainer.html('');
            _.each(data, function(el){
                var child = me.createChild(el);
                //console.log(el);
            });
            /*if (data.length == 0) {
                me.childrenContainer.append('Низший уровень вложенности');
            }*/
            me.searched = true;

        });
    };
    /**
     * Функция, отвечающая за раскрывание списка дочерних элементов
     */
    me.opened = false;
    me.toggle = function(noExpandedChange){
        me.opened = !me.opened;
        if (me.searched) {
            me.childrenContainer.toggle(500);
        } else {
            me.getChildren();
        }
        /*if (!noExpandedChange) {
            me.tree.toggleExpanded(me.id);
        }*/
        me.expandEl.toggleClass('opened');
        me.tree.setExpanded(me.id,me.expandEl.hasClass('opened'));
    };
    me.setOpened = function(val){
        me.opened = val;
        if (me.opened) {
            if (me.searched) {
                me.childrenContainer.show(500);
            } else {
                me.getChildren();
            }
            me.expandEl.addClass('opened');
            me.tree.setExpanded(me.id,true);
        } else {
            me.childrenContainer.hide(500);
        }
    };
    me.setSelected = function(val){
        if (val) {
            me.selected = true;
            me.tree.setExpanded(me.parent.id, true);
            TreeBranch.prototype.lastSelected = me;
            me.element.addClass('selected');
        } else {
            me.selected = false;
            TreeBranch.prototype.lastSelected = null;
            me.element.removeClass('selected');
        }
    };
    me.setSelected(false);
    me.toggleSelected = function(){
        me.selected = !me.selected;
        me.element.toggleClass('selected');
        if (me.selected) {
            TreeBranch.prototype.lastSelected = me;
        } else {
            TreeBranch.prototype.lastSelected = null;
        }
    };
    return me;
}
ControlButton.prototype.hasAnythingCheck = function(collection){
    if (collection.length == 0) {
        alert('Не выбрано ни одного элемента.');
        return false;
    }
    return true;
};
ControlButton.prototype.tooManyCheck = function(collection){
    if (collection.length > 1) {
        return confirm('Выбрано более одного элемента. Применить действие к самому верхнему выбранному элементу?');
    }
    return true;
};
ControlButton.prototype.actionForOneCountChecks = function(collection){
    if (ControlButton.prototype.hasAnythingCheck(collection)) {
        return ControlButton.prototype.tooManyCheck(collection);
    } else {
        return false;
    }
};
function ControlButton(value, className, callback, tree, param, preValidator){
    var me = {};
    //Сохраняем дерево, тк именно оно даст элементы
    me.tree = tree;
    if (!className) {className = '';}
    if (!param) {param = '';}

    if (typeof preValidator != 'function') {
        me.preValidator = ControlButton.prototype.hasAnythingCheck;
    } else if (preValidator === true) {
        me.preValidator = function(){return true};
    } else{
        me.preValidator = preValidator;
    }

    //Создаем элемент кнопки. Все стили накладываются внешне.
    me.element = $('<span>', $.extend({
        "class":"button " + className
    },param)).append(value);
    //Вешаем действие кнопки.
    me.element.click(function(event){
        //Если нет элементов, то и делать нечего
        if (me.tree) {
            //Ищем все элементы
            var elems = me.tree.getSelected();
            if (me.preValidator(elems)) {
                //Не всегда действие можно применить ко всем элементам.
                var stop = false;
                _.each(elems, function (el) {
                    if (!stop) {
                        stop = callback(el, event, elems);
                    }
                });
            }
        } else {
            alert('no tree');
        }
    });
    me.tree.addButton(me);
    return me;
}
function TreeStructure(url, param){
    var me = {};

    me.url = url;
    if (!param) {
        param = {};
    }
    param = $.extend({
        method: "post",
        id: 0,
        name: '',
        childFunc: TreeBranch,
        clickHandler: function(e) {return true;},
        toHref: function(){
            if (this.id) {
                return '#';
            }
        },
        extra:{
            hasChildren: 1
        }
    },param);
    me.childrenContainer = $('<ul>',{
        "class":"treeRoot"
    });
    me.childFunc = param.childFunc;
    me.clickHandler = param.clickHandler;
    me.toHref = param.toHref;
    me.generateButtons = param.generateButtons;
    me.tree = me;
    me.element = $("#TreeContainer");
    me.element.html(me.childrenContainer);

    //Задаем имя, в котором хранить информацию
    me.cookieName = 'TreeExpandedIds'+me.element.attr('id');
    //Получаем айдишники развернутых пунктов
    var cookie;
    me.expandedIds = [];
    if (cookie = $.cookie(me.cookieName)) {
        me.expandedIdsInitial = JSON.parse(cookie);
    }

    if (!(me.expandedIdsInitial instanceof Array)) {
        me.expandedIdsInitial = [];
    }

    me.expandedIdsInitial = _.unique(me.expandedIdsInitial);
    me.toggleExpanded = function(id){
        var ind = me.expandedIds.indexOf(id);
        if (ind != -1) {
            me.expandedIds.splice(ind, 1);
        } else {
            me.expandedIds.push(id);
        }
        //Сохраняем результат
        $.cookie(me.cookieName, JSON.stringify(me.expandedIds));
        //console.log(me.expandedIds);
    };
    me.setExpanded = function(id, val){
        var state = me.expandedIds.indexOf(id) != -1;

        if (state != val) {
            me.toggleExpanded(id);
        }
    };

    //Важно, чтобы первый элемент создавался именно здесь, иначе в его параметры
    // попадут лишние функции - будет illigal invokation
    me.firstEl = me.childFunc(me, param);
    me.firstEl.toggle();
    if (!param.container) {
        param.container = $("body");
    }

    me.buttonContainer = $("<div>",{
        "class":"controls"
    });
    me.addButton = function(button){
        me.buttonContainer.append(button.element);
    };
    if (typeof param.generatePanel == 'function') {
        console.log(me);
        param.generatePanel(me);
    }
    me.element.prepend(me.buttonContainer);

    me.unselectAll = function(){
        //alert('implement unselectAll');
        me.firstEl.iterateOverSelfAndDescendants(function(el){
            el.setSelected(false);
        });
    };

    me.getSelected = function(){
        console.log('getselected');
        var toGive = [];
        me.firstEl.iterateOverSelfAndDescendants(function(elem){
            if (elem.selected) {
                toGive.push(elem);
            }
        });
        //console.log(toGive);
        return toGive;
    };

    return me;
}
function addButtons(branch){
    if (!branch) {return;}
    if (!branch.parent.parent) {return;}


    branch.dragButton = $("<span>",{
        "class":"dragButton button"
    });
    branch.element.draggable({
        handle: branch.dragButton,
        helper:function(){ return branch.textEl.clone(); },
        cursorAt:{left:10},
        scope:'task'
    });
    branch.element.attr('data-id',branch.id);
    branch.textEl.droppable({
        hoverClass:'over',
        scope:'task',
        drop:function(event, ui){
            location.href = baseUrl + '/task/move/'+ ui.draggable.attr('data-id') +'/to/'+ branch.id;
        }
    });
    branch.buttonContainer.append(branch.dragButton);
    var status = branch.extra;
    var imageName = '';
    var imageAlt = '';
    //console.log(branch.extra);
    if (status.accepted == 1) {
        imageName = 'tick_small.png';
        imageAlt = 'Принято';
    } else if (status.handedIn == 1) {
        imageName = 'handedIn.png';
        imageAlt = 'Задание сдано';
    } else if (status.QHandedIn == 1) {
        imageName = 'QHandedIn.png';
        imageAlt = 'Просьба рассмотреть';
    } else if (status.notEmpty == 1){
        imageName = 'writing.png';
        imageAlt = 'Текст в разработке';
    } else if (status.keysGenerated > 0) {
        imageName = 'empty.png';
        imageAlt = 'Ожидает начала работы автора';
    } else if (status.hasKeys > 0) {
        imageName = 'keys_loaded.png';
        imageAlt = 'Ключи загружены';
    } else {
        imageName = 'new.png';
        imageAlt = 'Задание пустое';
    }
    branch.buttonContainer.append($('<img>',{
        src:baseUrl+'/images/'+imageName,
        alt: imageAlt,
        title: imageAlt,
        css:{
            height:'20px'
        }
    }));
    if (status.noAuthor == 1) {
        branch.buttonContainer.append($('<img>',{
            src:baseUrl + '/images/missing.png',
            alt: 'Нет автора',
            title: 'Нет автора',
            css:{
                height:'20px'
            }
        }));
        branch.element.addClass('noAuthor');
    }
}
function genControlPanel (tree) {
    if (!tree) {return;}

    //Добавляем список авторов.
    var authorList = $("#authorsList").detach();
    if (!authorList.length) {
        authorList = false;
    }

    console.log(authorList);

    new ControlButton("","edit",function(el){location.href = baseUrl + "/task/edit/" + el.id; return true;},tree, {},ControlButton.prototype.actionForOneCountChecks);
    new ControlButton("","keys",function(el){location.href = baseUrl + "/cabinet/loadKeywords/" + el.id; return true;},tree, {},ControlButton.prototype.actionForOneCountChecks);
    new ControlButton("","look",function(el){location.href = baseUrl + "/task/" + el.id; return true;},tree,{}, ControlButton.prototype.actionForOneCountChecks);
    var authorSelector = $("#authorForAssignSelect");
    new ControlButton("","assign_author",function(el){
        $.post(baseUrl + "/Task/assignAuthor/" + el.id, {
            author: authorList.val()
        },null, "JSON");
    },tree,{}, function(coll){ if (!coll) {return false;} else if (coll.length > 0) {return confirm("Вы собираетесь присвоить "+coll.length+" заданий автору "+$(authorList).select2('data')[0].text)+". Ok?"; } else {return false;} });

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
    new ControlButton("","delete_keys",function(el){$.post(baseUrl+"/Task/deleteKeys/"+el.id).done(function(){location.reload();});},tree,{},
        function (coll) {if (coll.length) {return confirm("Вы собираетесь удалить поисковые фразы и ключевые слова у " + coll.length + " заданий. Это действие необратимо. Продолжить все равно?");} else {return false;}});
    if (authorList) {
        tree.buttonContainer.append(authorList);
        authorList.select2();
    }
}
function toEdit($el, url, defText, callback) {
    if ($el) {
        $el.dblclick(function () {
            $el.hide();
            var val = defText;
            var saveVal = $el.html();
            if (!val) {
                val = saveVal;
            }

            var form = $("<form>",{
                css:{margin:0}
            });
            var inp = $("<input>", {
                type: "text",
                value: val
            });
            form.append(inp);
            $el.after(form);
            if (typeof url == 'function') {
                url = url.call($el);
            }
            function sendRequest(){
                var repl = $("<span>...</span>");
                form.replaceWith(repl);
                form = repl;
                var toSetVal = inp.val();
                function showName() {
                    form.remove();
                    $el.show();
                }
                if (saveVal != toSetVal) {
                    $.post(url, {
                        entered: toSetVal
                    }, null, "JSON").done(function (data) {
                        $el.html(data.toShow);
                        if (typeof callback == 'function') {
                            callback.call($el, data);
                        }
                        showName();
                    }).fail(function () {
                        showName();
                        alert('Ошибка!');
                    });
                } else {
                    showName();
                }
                return false;
            }
            var tempId = "handler" + Math.round(Math.random()*100000) + 1;
            inp.attr("data-handler",tempId);
            body.on("click."+tempId, function(event){
                console.log(event.target);
                if ($(event.target).attr("data-handler") != tempId) {
                    sendRequest();
                    body.off("click."+tempId);
                }
            });
            form.submit(sendRequest);
        });
    }
}