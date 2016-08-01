/**
 * Created by user on 06.07.2016.
 * @require jQuery
 * @require Underscore.js
 * @require jQuery-ui.js
 */
Lexical.prototype.wordsPool = [];
Lexical.prototype.phrasesPool = [];
Lexical.prototype.initialPhrasesPool = [];
Word.prototype.showAll = function(){
    _.each(Lexical.prototype.wordsPool, function (word) {
        word.show();
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
            value: '',
            "class": "strict"
        }));
        me.element.append($('<input>', {
            type: 'text',
            name: baseFormInputName + '[phrases][morph][]',
            placeholder: 'Морфология',
            value: 1,
            "class": "morph"
        }));
        me.element.append(me.inputEl);
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
        //Контейнер для элеентов данного типа должен быть задан в прототипе.
        Phrase.prototype.container.append(me.element);
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
    return me;
}
function Word(text,param) {
    var me = new Lexical(text,param);
    me.num = param.num;
    me.counterCell = $('<td>',{"class":"counter"});
    me.stemCell = $('<td>',{"class":"stem"});
    me.textEl = $('<span>',{
        "class":"keywordText",
        css:{
            display:"inline-block"
        }
    }).html(text);
    me.element = new LinkedDom('tr',{
        "class":"word"
    },me)
        .append($('<td>', {"class":"fulltext"})
            .append(me.textEl))
        .append(me.stemCell)
        .append(me.counterCell)
        .append(me.num);
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
        console.log(Lexical.prototype.wordsPool);
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
        var ind = me.phrases.indexOf(phrase);
        me.phrases.splice(ind, 1);
        if (!noChangeCounter) {
            me.setCounter(me.counter + 1);
        }
    };
    //Если снаружи задан массив корней слова (должен иметь один элемент)
    if (param.stems) {
        me.stems = param.stems;
        me.onAfterAnalyze();
    }
    return me;
}
function LinkedDom(node, attrs, linkTo){
    var me = $('<'+node+'>',attrs);
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
    console.log(param);
    //Сохраняем свой id, иначе не будет детей
    me.id = param.id;
    //Сохраняем родителя, иначе не будет отображения на странице
    //Родителя полностью рекурсивно копируем, потом понадобится
    me.parent = $.extend({},parent);
    //Детей пока нет до первого нажатия
    me.children = [];
    //Детей пока не искали
    me.searched = false;
    //Элемент, отображающий ветку дерева
    me.element = $('<li>',{
        "class":"treeBranch"
    });
    //Таким образом сохраняем всевозможные атрибуты, передаваемые от родителя к детям
    //Среди них url, method и тд
    //При этом все новые методы/атрибуты должны замениться на дочерние
    //me = $.extend(parent, me);
    me.url = parent.url;
    me.toHref = parent.toHref;
    me.childFunc = parent.childFunc;
    me.method = parent.method;
    me.clickHandler = parent.clickHandler;
    //В нем содержится название ветки (этот же элемент будет отвечать за сворачивание)
    me.textEl = $('<div>',{
        "class":"branchName"
    });
    me.link = $('<a>',{href: me.toHref()});
    me.textEl.html(me.link.append(param.name));
    me.textEl.click(function(e){
        if (me.clickHandler(e)){
            me.toggle();
        }
    });
    //И элемент с детьми
    me.element.append(me.textEl);
    me.childrenContainer = $('<ul>',{
        "class":"branchChildren",
        css:{
            display:"none"
        }
    });
    me.element.append(me.childrenContainer);

    //Присваиваем элемент контейнеру
    me.parent.childrenContainer.append(me.element);
    /**
     * Отвечает за создание детей. Обращается на сервер и получает своих потомков,
     * затем инициализирует их
     */
    me.getChildren = function(){
        me.childrenContainer.toggle();
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
                me.children.push(me.childFunc(me, el));
            });
            if (data.length == 0) {
                me.childrenContainer.append('Низший уровень вложенности');
            }
            me.searched = true;

        });
    };
    /**
     * Функция, отвечающая за раскрывание списка дочерних элементов
     */
    me.toggle = function(){
        if (me.searched) {
            me.childrenContainer.toggle(500);
        } else {
            me.getChildren();
        }
    };
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
        }
    },param);
    console.log(param);
    me.childrenContainer = $('<ul>',{
        "class":"treeRoot"
    });
    me.childFunc = param.childFunc;
    me.clickHandler = param.clickHandler;
    me.toHref = param.toHref;
    me.firstEl = me.childFunc(me, param);
    me.firstEl.toggle();
    if (!param.container) {
        param.container = $("body");
    }
    $("#TreeContainer").append(me.childrenContainer);
    return me;
}