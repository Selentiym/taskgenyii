<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 08.08.2016
 * Time: 12:30
 */
/**
 * @type User $author
 */
if (!$author) {
    $author = User::model() -> findByPk($id_sender);
}
?>
<div class="letter <?php echo $class;?> <?php echo $opened == 0 ? 'new':'';?>">
    <div class="pane leftPane">
        <div class="author"><?php echo $author -> name; ?></div>
        <div class="date"><?php echo $sent; ?></div>
    </div>
    <div class="pane rightPane">
        <?php echo $text; ?>
    </div>
</div>
