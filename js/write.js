/**
 * Created by user on 11.07.2016.
 * @require jQuery
 */
function analyze(id, text, rez) {

    $.post(baseUrl + '/text/analyze/'+id, {
        text: text
    },function(){},"JSON").done(function(data) {
        rez.html('Текст без тегов:<br/>' + data.text);
        console.log(data);
    });
}