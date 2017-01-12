<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.08.2016
 * Time: 12:24
 */
/**
 * @type Task $model
 */
Yii::app() -> getClientScript() -> registerCssFile(Yii::app() -> baseUrl . '/css/history.css');
//$this -> renderPartial('//_navBar');
$texts = $model -> texts;
if (empty($texts)) {
	$texts = [];
}
if ($t = $model -> prepareTextModel()) {
	$model -> currentText = $t;
	array_unshift($texts, $t);
}
$editor = Yii::app() -> user -> checkAccess('editor');
$cur = $model -> getRelated('currentText', true);
foreach ($texts as $text) {

	if ($text -> id != $cur -> id) {
		$view = '//text/_history';
	} elseif ($model -> rezult) {
		if ($editor) {
			echo "<h1>Текст принят! Только для небольших правок!</h1>";
			$this -> renderPartial('//text/write', ['model' => $text]);
			$this -> renderPartial('//text/_history', ['model' => $text]);
			$view = false;
		} else {
			$view = '//text/_history';
		}
	} else {
		$view = '//text/write';
	}
	if ($view) {
		$this->renderPartial($view, ['model' => $text]);
	}
}
$this -> renderPartial('//pattern/common',['model' => $model]);
?>

