<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 13.09.2016
 * Time: 22:00
 */
/**
 * @type Text $model
 */
if ($model -> accepted) {
    $class = "tick";
    $text = "Принят";
} elseif ($model -> handedIn) {
    $class = "time";
    $text = "Сдан";
} elseif ($model -> QHandedIn) {
    $class = "time";
    $text = "Просьба принять";
} else {
    $class = "cross";
    $text = "В разработке";
}
if ($author = $model -> task -> author) {
    $author = $author -> name;
} else {
    $author = "Нет автора";
}
?>
<span class="highlightTicksAndCrosses">
    <span class="author">
        <?php echo $author; ?>
    </span>,
    <span class="date">
        <?php echo $model -> updated; ?>
    </span>,
    <span class="<?php echo $class; ?>"><?php echo $text; ?></span>

</span>
