<?php
/**
 * @type Task $task
 */
?>
<li>
<a href="<?php echo Yii::app() -> createUrl('task/view', array('arg' => $task -> id)); ?>"><?php echo $task -> name; ?></a>
<?php
    $text = $task -> currentText;
if ($text -> accepted == 1) {
    $imageName = 'tick_small.png';
    $imageAlt = 'Принято';
} else if ($text -> handedIn == 1) {
    $imageName = 'handedIn.png';
    $imageAlt = 'Задание сдано';
} else if ($text -> QHandedIn == 1) {
    $imageName = 'QHandedIn.png';
    $imageAlt = 'Просьба рассмотреть';
} else if (mb_strlen(arrayString::leaveOnlyLetters($text -> text)) > 100){
    $imageName = 'writing.png';
    $imageAlt = 'Текст в разработке';
} else {
    $imageName = 'empty.png';
    $imageAlt = 'Пока что ничего нет';
}
?>
    <img src="<?php echo Yii::app() -> baseUrl . '/images/'.$imageName; ?>" alt="<?php echo $imageAlt; ?>" title="<?php echo $imageAlt; ?>"/>
</li>
