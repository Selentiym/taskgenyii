<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.07.2016
 * Time: 12:14
 */
//Yii::app() -> UrlHelper -> wanted();
$this -> renderPartial('_navBar');
?>
У Вас недостаточно прав ля просмотра данной страницы. Вы можете <a href="<?php echo Yii::app() -> baseUrl; ?>/logout">выйти и войти под другим именем</a>.
