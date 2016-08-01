/**
 * Created by user on 01.08.2016.
 *
 * @require jQuery
 *
 * Содержит скрипты, отвечающие за добавление коммента и, возможно, в дальнейшем
 * за их отображение и обновление.
 */
$('.goComment').click(function(){
    var form = $(this).parents().filter('.commentForm');
    var container = $(this).parents().filter('.comments').children('.olderOnes');
    console.log(container);
    $.post(baseUrl + '/comment/createComment',form.serialize()).done(function(data){
        container.prepend(data);
        form.children('textarea').val('');
    });
});