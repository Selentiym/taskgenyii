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
<p>Минимальная уникальность текста (в системе): <strong><?php echo $params['unique']; ?></strong></p>
<p>Максимальный процент совпадений текста в сиситеме (перекрестная уникальность): <strong><?php echo $params['cross']; ?></strong></p>
<p>Максимальная тошнотность: <strong><?php echo $params['sick']; ?></strong></p>
<p>Максимальный процент вхождения самого частого слова: <strong><?php echo $params['word']; ?></strong></p>
</div>