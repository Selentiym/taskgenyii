/**
 * Created by user on 11.07.2016.
 * @require jQuery, underscore.js
 */
function text(id) {
    var me = {};
    me.id = id;
    alert(me.id);
    me.seoInfo = $("#seoData");
    me.rez = $('#rezultDiv');
    me.check = $("#check");
    me.check.click(function(){
        me.analyze();
    });
    me.falseChecks = function() {
        me.checks = {};
        me.checks.sickCheck = false;
        me.checks.firstNuclCheck = false;
        me.checks.firstWordCheck = false;
        me.checks.uniqueCheck = false;
        me.checks.toSubmit = false;
        me.checks.keyCheck = false;
    };
    me.analyze = function () {
        me.falseChecks();
        me.text = tinyMCE.activeEditor.getContent();
        $.post(baseUrl + '/text/analyze/' + me.id, {
            text: me.text
        }, function () {
        }, "JSON").done(function (data) {
            console.log(data);
            me.rez.html('Текст без тегов:<br/>' + data.text);
            _.each(data.phrs.direct, function (el, key) {
                var dom = $('#direct' + key);
                dom.html(el + '/' + dom.attr('data-mustHave'));
            });
            _.each(data.phrs.morph, function (el, key) {
                var dom = $('#morph' + key);
                dom.html(el + '/' + dom.attr('data-mustHave'));
            });
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
        me.seoDataAdd = function(data){
            if (!data.fromAjax) {
                data = {};
            }
            me.seoInfo.html('');

            me.seoInfo.append($('<div>').html('Тошнотность: '+data.sick));
            me.seoInfo.append($('<div>').html('Первый показатель в семантическом ядре: '+data.first_nucl_num).append(data.first_nucl));
            me.seoInfo.append($('<div>').html('Первый показатель в словах: '+data.first_word_num).append(data.first_word));
        };
    };
    return me;
}