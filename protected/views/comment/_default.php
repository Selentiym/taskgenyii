<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 01.08.2016
 * Time: 14:15
 */
/**
 * @type Comment $model
 */
?>
<div class="comment">
    <div class="head">
        <div class="author">
            <?php echo $model -> author -> show(); ?>
        </div>
        <div class="date">
            <?php echo $model -> date; ?>
        </div>
    </div>
    <div class="in-container">
        <?php echo $model -> comment; ?>
    </div>
</div>

