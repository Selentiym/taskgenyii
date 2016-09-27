<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 27.09.2016
 * Time: 21:42
 */
?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'author-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // There is a call to performAjaxValidation() commented in generated controller code.
    // See class documentation of CActiveForm for details on this.
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));?>
<fieldset>

    <div class="well">
        <div class="form-group">
            <label for="name">Отображаемое имя</label>
            <?php echo $form->textField($model, 'name',array('size'=>60,'maxlength'=>255,'placeholder'=>'Имя')); ?>
        </div>
        <div class="form-group">
            <label for="username">Логин</label>
            <?php echo $form->textField($model, 'username',array('size'=>60,'maxlength'=>255,'placeholder'=>'Логин')); ?>
        </div>
    </div>
    <div class="well">
        <div class="form-group">
            <label for="input_password">Пароль</label>
            <?php echo UHtml::passwordField('Author[input_password]', '',array('size'=>60,'maxlength'=>255,'class' => 'form-control' )); ?>
        </div>
        <div class="form-group">
            <label for="input_password">Повторите пароль</label>
            <?php echo UHtml::passwordField('Author[input_password_second]', '',array('size'=>60,'maxlength'=>255,'class' => 'form-control' )); ?>
        </div>
    </div>
    <?php echo UHtml::submitButton($model->isNewRecord ? CHtml::encode('Создать') : CHtml::encode('Сохранить')); ?>
</fieldset>
<?php $this -> endWidget(); ?>