/**
 * Created by user on 07.08.2016.
 */
Dialog.prototype.opened = {};
function Dialog(container, idSender, idReceiver, date) {
    var me = {};
    me.portionSize = 5;
    me.lastDate = date;
    me.firstDate = date;
    console.log(me.firstDate);
    me.idSender = idSender;
    me.idReceiver = idReceiver;
    me.shortcut = $("#"+container.attr('id')+"_shortcut");
    me.container = container;
    if (me.shortcut.length > 0) {
        container.hide();
        me.shortcut.append(me.container.find('.talkerName').html());
        me.shortcut.click(function(){
            me.container.toggle(500);
        });
    }

    me.container.removeClass('hidden');
    console.log(me.container);
    me.letters = container.find('.letters');
    me.form = container.find('.form');
    me.textArea = container.find('.input');
    me.moreButton = container.find('.moreSpan');
    me.sendButton = container.find('.send');
    me.minifyButton = container.find('.minify');
    me.closeButton = container.find('.close');
    if (me.minifyButton.length > 0) {
        me.minifyButton.click(function () {
            me.container.toggle(500);
        });
    }
    me.notRead = [];
    me.loadHistory = function(){
        if (!me.noMore) {
            $.post(baseUrl + '/dialog/history', {
                date: me.lastDate,
                size: me.portionSize,
                idSender: me.idSender,
                idReceiver: me.idReceiver
            }, function () {
            }, "JSON")
            .done(function (data) {
                me.analyzeReply(data);
                me.letters.prepend(data.html);
                me.lastDate = data.lastDate;
                if (data.noMore) {
                    me.noMore = true;
                    me.moreButton.html('Достигнут конец истории.');
                    me.moreButton.unbind('click');
                }
                if (me.scroll) {
                    me.letters.scrollTop(me.letters.prop("scrollHeight"));
                }
            });
        }
    };
    me.moreButton.click(function(){
        me.loadHistory();
    });
    me.send = function(){
        var text = me.textArea.val();
        if (text) {
            $.post(baseUrl + '/dialog/send',{
                idSender: me.idSender,
                idReceiver: me.idReceiver,
                text: text
            }, function(){}, "JSON")
                .done(function(data){
                    if (data.success) {
                        me.textArea.val('');
                        me.loadNewLetters();
                        me.scroll = true;
                    } else {
                        console.log(data);
                        if (data.error) {
                            me.warn(data.error);
                        } else {
                            me.warn('Сообщение не отправлено по какой-либо причине.');
                        }
                    }
                });
        }
    };
    me.sendButton.click(me.send);
    me.loadNewLetters = function(){
        if (me.newIntervalID) {
            clearTimeout(me.newIntervalID);
            me.newIntervalID = false;
        }
        $.post(baseUrl + '/dialog/checkNewLetters',{
            idSender:me.idSender,
            idReceiver:me.idReceiver,
            date:me.firstDate
        },function(){},"JSON").done(function(data){
            if (data.success) {
                me.letters.append(data.html);
                me.firstDate = data.firstDate;
                if (me.scroll) {
                    me.letters.scrollTop(me.letters.prop("scrollHeight"));
                }
                me.analyzeReply(data);
            }
            me.newIntervalID = setTimeout(me.loadNewLetters, me.newInterval);
            me.scroll = false;
        });
    };
    me.afterDataAnalyze = function(){};
    if (me.textArea.length) {
        me.textArea.keydown(function(e){
            if (e.keyCode == 13) {
                if (e.ctrlKey) {
                    me.send();
                }
            }
        });
        me.textArea.focus(function(){
            me.ReadEverything();
        });
    } else {
        me.afterDataAnalyze = function () {
            setTimeout(me.ReadEverything, 5000);
        };
    }
    me.analyzeReply = function (data) {
        if (data.notRead instanceof Array) {
            me.notRead = me.notRead.concat(data.notRead);
            me.afterDataAnalyze();
        }
    };
    me.ReadEverything = function () {
        if (me.notRead.length) {
            $.post(baseUrl + '/dialog/read', {
                toRead: me.notRead
            },function () {},"JSON").done(
                function(data){
                    if (data.success) {
                        me.notRead = [];
                        me.letters.children().filter('.opponent.new').removeClass('new');
                    }
                }
            );
        }
    };
    me.warn = function(str){
        if (str) {
            me.letters.append($('<div>',{
                "class":"warn"
            }).append(str));
        }
    };
    me.scroll = true;
    me.loadHistory();
    me.loadNewLetters();
    me.newInterval = 10000;
    me.newIntervalID = false;
    //Сохранили диалог в массив.
    if (!Dialog.prototype.opened) {
        Dialog.prototype.opened = {};
    }
    Dialog.prototype.opened[me.container.attr('id')] = me;
    return me;
}
function addNewDialogFromClick(event) {
    addNewDialog($(this).attr('data-id'));
}
function addNewDialog(id){
    if (!Dialog.prototype.opened[id]) {
        $.post(baseUrl + '/dialog/open/'+id,{},function(){},"JSON").done(function(data){
            console.log(data);
            if (!data.no) {
                Dialog.prototype.container.append(data.html);
                var created = new Dialog($('#' + id), data.idSender, data.idReceiver, data.date);
                var parent = created.container.parent();
                if (parent.filter('.toDrag').length) {
                    parent.draggable();
                }
            }
        });
    } else {
        alert('Диалог уже открыт!');
    }
    event.preventDefault();
}