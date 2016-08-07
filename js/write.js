/**
 * Created by user on 11.07.2016.
 * @require jQuery, underscore.js, tinymce
 */
var form = $('#textForm');
$('#send').click(function(){
    form.attr('action',baseUrl + '/text/handIn/'+form.attr('data-id'));
    window.textObj.goSubmit(form);
});
$('#sendWithMistakes').click(function(){
    form.attr('action',baseUrl + '/text/handInWithMistakes/'+form.attr('data-id'));
    window.textObj.goSubmit(form);
});
$('#accept').click(function(){
    form.attr('action',baseUrl + '/text/accept/'+form.attr('data-id'));
    form.submit();
    //window.textObj.goSubmit(form);
});
$('#decline').click(function(){
    form.attr('action',baseUrl + '/text/decline/'+form.attr('data-id'));
    form.submit();
    //window.textObj.goSubmit(form);
});

$('#delay').click(function(){
    form.attr('action',baseUrl + '/text/save/'+form.attr('data-id'));
    form.submit();
});
var loaderImage = $('<img>',{
    src:baseUrl + '/images/loading.gif',
    alt:'loader',
    css:{
        width:'20px',
        height:'20px'
    }
});
function text(id) {
    var me = {};
    me.id = id;
    me.seoInfo = $("#seoData");
    me.rez = $('#rezultDiv');
    me.uniqueCont = $("#unique");
    me.crossUnique = $("#crossUnique");
    me.check = $("#check");
    me.check.click(function(){
        me.analyze();
    });
    me.uniqueButton = $("#uniqueButton");
    me.uniqueButton.click(function(){

        me.uniqueRequest();
    });
    me.contentChanged = function () {
        me.falseChecks();
        me.text = tinyMCE.activeEditor.getContent();
    };
    me.falseChecks = function() {
        me.checks = {};
        me.checks.sickCheck = false;
        me.checks.firstNuclCheck = false;
        me.checks.firstWordCheck = false;
        me.checks.uniqueCheck = false;
        me.checks.toSubmit = false;
        me.checks.keyCheck = false;
        me.changedSinceUnique = true;
    };
    //Когда страница только загрузилась, нужно поставить все флаги.
    me.falseChecks();
    me.trySubmit = function () {
        if (me.checks.toSubmit) {
            var submit = true;
            if (!me.checks.sickCheck) {
                me.log('Проверьте тошностность!');
                submit = false;
            }
            if (!me.checks.firstNuclCheck) {
                me.log('Проверьте первый показатель в семантическом ядре!');
                submit = false;
            }
            if (!me.checks.firstWordCheck) {
                me.log('Проверьте первый показатель в словах!');
                submit = false;
            }
            if (!me.checks.uniqueCheck) {
                me.log('Проверьте уникальность!');
                submit = false;
            }
            if (!me.checks.keyCheck) {
                me.log('Проверьте ключевые слова!');
                submit = false;
            }
            if (submit) {
                me.form.submit();
            }
        }
    };
    me.log = function(str){
        console.log(str);
    };
    me.analyze = function () {
        me.falseChecks();
        me.text = tinyMCE.activeEditor.getContent();
        $.post(baseUrl + '/text/analyze/' + me.id, {
            text: me.text
        }, function () {
        }, "JSON").done(function (data) {
            //console.log(data);
            //Присваиваем текст с подстветкой
            me.rez.html('Текст с подсветкой ключевых слов:<br/>' + data.text);
            var tempCur = 0;
            var tempMax = 0;
            _.each(data.phrs.direct, function (el, key) {
                var dom = $('#direct' + key);
                tempMax ++;
                var mustHave = dom.attr('data-mustHave');
                dom.html(el + '/' + mustHave);
                if (el >= mustHave) {
                    tempCur ++;
                }
            });
            var directFlag = (tempCur == tempMax);
            tempCur = 0;
            tempMax = 0;
            _.each(data.phrs.morph, function (el, key) {
                var dom = $('#morph' + key);
                tempMax ++;
                var mustHave = dom.attr('data-mustHave');
                dom.html(el + '/' + mustHave);
                if (el >= mustHave) {
                    tempCur ++;
                }
            });
            var morphFlag = (tempCur == tempMax);
            me.checks.keyCheck = morphFlag && directFlag;
        });
        if (me.text) {
            $.post(baseUrl + '/text/seoStat/' + me.id, {
                text: me.text
            }, function () {
            }, "JSON").done(function (data) {
                data.fromAjax = true;
                me.seoDataAdd(data);
            });
        }
    };
    me.seoDataAdd = function(data){
        if (!data.fromAjax) {
            data = {};
        }
        me.seoInfo.html('');

        me.checks.sickCheck = data.sick <= 9;
        me.checks.firstNuclCheck = data.first_nucl_num <= 4;
        me.checks.firstWordCheck = data.first_word_num <= 4;

        me.seoInfo.append($('<div>').html('Тошнотность: '+data.sick));
        me.seoInfo.append($('<div>').html('Первый показатель в семантическом ядре: '+data.first_nucl_num).append(data.first_nucl));
        me.seoInfo.append($('<div>').html('Первый показатель в словах: '+data.first_word_num).append(data.first_word));
    };
    //Отправляет запрос на проверку.
    me.uniqueRequest = function(callback){
        if (me.changedSinceUnique) {
            me.analyze();
            me.uid = false;
            me.counter = 0;
            me.changedSinceUnique = false;
            me.uniqueCont.append(loaderImage.clone());
            if (me.obtainInterval) {
                clearTimeout(me.obtainInterval);
                me.obtainInterval = false;
            }
            $.post(baseUrl + '/text/giveCrossUnique/' + me.id,{
                text: me.text
            },function(){},"JSON").done(function (data) {
                var phrases = '';
                console.log(data);
                if (data.matches) {
                    phrases = data.matches.join('<br/>');
                }
                if (data.percent == -1) {
                    me.crossUnique.html('Не найдено похожих текстов в базе данных.');
                } else {
                    var textCont = $('<div>',{
                        "id": "textCont",
                        css: {display:"none"}
                    }).html($('<div>').html(data.text)).append($('<div>').html(phrases));
                    me.crossUnique.html('');
                    me.crossUnique.append($('<span>',{
                        "id": "crossPercent",
                        css:{
                            cursor:"pointer"
                        }
                    }).html('Максимальный процент совпадений среди текстов в системе: ' + data.percent).click(function(){
                        textCont.toggle();
                    }));
                    me.crossUnique.append(textCont);
                }
            });
            $.post(baseUrl + '/text/uniqueCheck/' + me.id, {
                text: me.text
            }, function () {
            }, "JSON").done(function (data) { console.log(data);
                if (typeof callback === 'function') {
                    //Вызываем переданную функцию
                    callback();
                }
                if (data.text_uid) {
                    me.uid = data.text_uid;
                    me.once = false;
                    me.obtainUnique();
                }
            });
        } else {
            alert('Не было изменений с последней проверки уникальности.');
        }
    };
    me.obtainUniqueOnce = function(){
        me.once = true;
        me.obtainUnique();
    };
    //Проверяет, есть ли ответ
    me.obtainUnique = function(){
        $.post(baseUrl + '/text/giveUnique/' + me.id, {
        }, function () {
        }, "JSON").done(function (data) {
            if (!data) {data = {};}
            if (data.unique) {
                if (!me.once) {
                    if (data.uid != me.uid) {
                        alert('Идентификаторы проверок не совпадают. Данные по уникальности могут быть неверными.');
                    }
                }
                me.unique = data.unique;
                me.uid = data.uid;
                me.changedSinceUnique = false;
                me.renderUnique();
            } else {
                me.counter ++;
                if ((me.counter < 20)&&(!me.once)) {
                    me.obtainInterval = setTimeout(function () {
                        me.obtainUnique();
                    }, 10000);
                } else {
                    alert('Не удалось получить уникальность.');
                    me.renderUnique();
                    me.obtainInterval = false;
                }
            }
        });
    };
    me.renderUnique = function() {
        if ((me.unique) && (me.uid)) {
            me.uniqueCont.html("Уникальность: ").append($('<a>', {
                href: 'http://text.ru/antiplagiat/' + me.uid
            }).append(me.unique));
        } else {
            me.uniqueCont.html('Проверка на уникальность не проводилась или завершена с ошибкой.');
        }
    };
    me.goSubmit = function (form) {
        if (me.changedSinceUnique) {

            tinymce.activeEditor.getBody().setAttribute('contenteditable', false);
            me.text = tinyMCE.activeEditor.getContent();
            var cont = $("#editorBlock");
            cont.after(me.text);
            cont.hide();
            me.uniqueRequest(function(){
                form.submit();
            });
        } else {
            form.submit();
        }
    };
    //А вдруг уже есть сохраненная уникальность с самого начала.
    me.obtainUniqueOnce();
    return me;
}