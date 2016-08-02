<?php
/**
 * @type Text $model
 */
?>
<div class="textHistoryShortcut">
    <div class="head highlightTicksAndCrosses">
        <span class="author">
            <?php echo $model -> task -> author -> name; ?>
        </span>,
        <span class="date">
            <?php echo $model -> updated; ?>
        </span>,
        <span class="<? echo $model -> handedIn ? 'tick' : 'cross' ?>">Сдан</span>,
        <span class="<? echo $model -> QHandedIn ? 'tick' : 'cross' ?>">Просьба проверить</span>,
        <span class="<? echo $model -> accepted ? 'tick' : 'cross' ?>">Принят</span>
    </div>
    <div class="body">
        <?php echo $model -> text; ?>
    </div>
    <?php $this -> renderPartial('//comment/_comments', ['model' => $model]); ?>
</div>
