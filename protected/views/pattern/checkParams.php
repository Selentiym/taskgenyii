<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.01.2017
 * Time: 16:24
 */
?>
<div>
    <?php $params = $model -> getCheckParams(); ?>
<p>Минимальная уникальность текста: <strong><?php echo $params['unique']; ?></strong></p>
<p title="Максимальный процент совпадений текста в сиситеме">Перекрестная уникальность: <strong><?php echo $params['cross']; ?></strong></p>
<p>Максимальная академическая тошнота: <strong><?php echo $params['sick']; ?></strong></p>
<p title="Максимальный процент вхождения самого частого слова">Первый показатель в словах: <strong><?php echo $params['word']; ?></strong></p>
</div>