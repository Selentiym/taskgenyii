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
    me.lengthCont = $("#lengthContainer");
    me.lengthSpan = me.lengthCont.find("#length");
    var min = me.lengthCont.attr("data-min");
    if (min > 0) {
        me.lengthCont.prepend(min + ' < ');
    }
    var max = me.lengthCont.attr("data-max");
    if (max > 0) {
        me.lengthCont.append(' < ' + max);
    }
    me.check.click(function(){
        me.analyze();
    });
    me.uniqueButton = $("#uniqueButton");
    me.uniqueButton.click(function(){

        //me.uniqueRequest();
        me.fastUnique();
    });
    me.descriptionEl = $("#description");
    me.descriptionEl.change(function(){
        me.description = me.descriptionEl.val();
        me.recountLength();
    });
    me.contentChanged = function () {
        me.falseChecks();
        me.recountLength();
        me.text = tinyMCE.activeEditor.getContent();
    };
    me.lengthInterval = false;
    me.setLength = function(val){
        me.lengthSpan.html(val);
    };
    me.recountLength = function(){
        me.text = tinyMCE.activeEditor.getContent();
        me.description = me.descriptionEl.val();
        if (me.lengthInterval) {
            clearTimeout(me.lengthInterval);
            me.lengthInterval = false;
        }
        $.post(baseUrl + '/text/length/' + me.id,{
            text:me.text,
            description:me.description
        },null,"JSON").done(function(data){
            me.setLength(data.length);
            me.lengthInterval = setTimeout(me.recountLength, 60*1000);
        });
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
                me.log('Проверьте академическую тошноту!');
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
        me.recountLength();
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
            if (data.phrs) {
                _.each(data.phrs.direct, function (el, key) {
                    var dom = $('#direct' + key);
                    tempMax++;
                    var mustHave = dom.attr('data-mustHave');
                    dom.html(el + '/' + mustHave);
                    if (el >= mustHave) {
                        tempCur++;
                    }
                });
                var directFlag = (tempCur == tempMax);
                tempCur = 0;
                tempMax = 0;
                _.each(data.phrs.morph, function (el, key) {
                    var dom = $('#morph' + key);
                    tempMax++;
                    var mustHave = dom.attr('data-mustHave');
                    dom.html(el + '/' + mustHave);
                    if (el >= mustHave) {
                        tempCur++;
                    }
                });
            }
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

        me.seoInfo.append($('<div>').html('Тошнота: '+data.sick));
        //me.seoInfo.append($('<div>').html('Первый показатель в семантическом ядре: '+data.first_nucl_num).append(data.first_nucl));
        me.seoInfo.append($('<div>').html('Первый показатель в словах: '+data.first_word_num + ', ').append('<b>'+data.first_word+'</b>'));
    };
    me.crossUniqueCheck = function(){
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
    };
    me.highlight = function(hl_array){
        var t_hl = me.cleanUniqueText.split(" ");
        for (var i = 0; i < hl_array.length; i++)
        {
            if (hl_array[i] instanceof Array) {
                t_hl[ hl_array[i][0] ] = "<b>" + (t_hl[ hl_array[i][0] ] === undefined ? "" : t_hl[ hl_array[i][0] ]);
                t_hl[ hl_array[i][1] ] = (t_hl[ hl_array[i][1] ] === undefined ? "" : t_hl[ hl_array[i][1] ]) + "</b>";
            } else {
                t_hl[ hl_array[i] ] = "<b>" + t_hl[ hl_array[i] ] + "</b>";
            }
        }
        me.uniqueDetail.html(t_hl.join(" "));
        return false;
    };
    me.fastUnique = function(callback){
        me.analyze();
        if (me.changedSinceUnique) {
            me.crossUniqueCheck();
            me.cleanUniqueText = '';
            me.uid = false;
            me.changedSinceUnique = false;
            me.uniqueCont.append(loaderImage.clone());
            $.post(baseUrl + '/text/fastUnique/' + me.id, {
                text: me.text
            }, function () {
            }, "JSON").done(function (data) {
                if (data.success) {
                    me.uniqueDetail = $('<div>',{
                        "class":"uniqueDetail",
                        css:{display:"none"}
                    }).html(loaderImage.clone());
                    me.showUniqueInfo = $('<span>',{
                        "class":"showUnique"
                    }).html(data.percent);
                    me.uniqueCont.html("Уникальность: ").append(me.showUniqueInfo).append(me.uniqueDetail);
                    me.showUniqueInfo.click(function(){me.uniqueDetail.toggle(1000);});
                    me.cleanUniqueText = data.text;
                    me.uniqueDetail.html(data.text);
                    /*if (data.highlight instanceof Array) {
                        var hl_array = data.highlight;
                        var t_hl = data.text.split(' ');
                        for (var i = 0; i < hl_array.length; i++) {
                            if (hl_array[i] instanceof Array) {
                                t_hl[hl_array[i][0]] = "<b>" + (t_hl[hl_array[i][0]] === undefined ? "" : t_hl[hl_array[i][0]]);
                                t_hl[hl_array[i][1]] = (t_hl[hl_array[i][1]] === undefined ? "" : t_hl[hl_array[i][1]]) + "</b>";
                            } else {
                                t_hl[hl_array[i]] = "<b>" + t_hl[hl_array[i]] + "</b>";
                            }
                        }
                        me.uniqueDetail.html(t_hl.join(" "));
                    }*/
                    if (data.matches instanceof Array) {
                        var table = $('<table>');
                        me.uniqueCont.append(table);
                        _.each(data.matches, function(match){
                            var line = $('<tr><td><a href="' + match['url'] + '" target="_blank">' + match['url'] + '</a></td>' +
                            '<td><strong>' + match['percent'] + '%</strong></td>'+
                            '<td><a class="show" href="#">подсветить совпадения</a></td></tr>');
                            line.find('.show').click(function(){
                                me.highlight(match['highlight']);
                            });
                            table.append(line);
                        });
                    }
                    me.changedSinceUnique = false;
                    if (typeof callback == 'function') {
                        callback();
                    }
                } else {
                    me.uniqueCont.children('img').remove();
                    alert(data.error);
                    me.changedSinceUnique = true;
                }
            });
        } else {
            alert('Не было изменений с прошлой проверки уникальности.');
        }
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
            me.crossUniqueCheck();
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
    me.obtainUnique = function() {
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
                    //alert('Не удалось получить уникальность.');
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
        } else if(me.unique) {
            me.uniqueCont.html("Уникальность: ").append($('<span>', {
            }).append(me.unique));
        } else {
            me.uniqueCont.html('Проверка на уникальность не проводилась или завершена с ошибкой.');
        }
    };
    me.goSubmit = function (form) {
        form.submit();
        /*if (me.changedSinceUnique) {

            tinymce.activeEditor.getBody().setAttribute('contenteditable', false);
            me.text = tinyMCE.activeEditor.getContent();
            var cont = $("#editorBlock");
            cont.after(me.text);
            cont.hide();
            me.fastUnique(function(){form.submit();});
            /*me.uniqueRequest(function(){
                form.submit();
            });*/
        /*} else {
            form.submit();
        }*/
    };
    //А вдруг уже есть сохраненная уникальность с самого начала.
    me.obtainUniqueOnce();
    return me;
}