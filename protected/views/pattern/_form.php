<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.09.2016
 * Time: 17:23
 */
?>
<?php
var_dump($model -> getErrors());
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'pattern-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // There is a call to performAjaxValidation() commented in generated controller code.
    // See class documentation of CActiveForm for details on this.
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));
/**
 * @type CActiveForm $form
 */
?>
    <fieldset>

        <div class="well">
            <div class="form-group">
                <label for="minUnique">Минимальная уникальность</label>
                <?php echo $form->numberField($model, 'minUnique',array('size'=>60,'maxlength'=>255)); ?>
            </div>
            <div class="form-group">
                <label for="maxSickness">Максимальная тошнотность</label>
                <?php echo $form->numberField($model, 'maxSickness',array('size'=>60,'maxlength'=>255)); ?>
            </div>
            <div class="form-group">
                <label for="maxCross">Максимальный процент совпадений в системе</label>
                <?php echo $form->numberField($model, 'maxCross',array('size'=>60,'maxlength'=>255)); ?>
            </div>
            <div class="form-group">
                <label for="maxSickness">Максимальный процент вхождения самого частого слова</label>
                <?php echo $form->numberField($model, 'maxWord',array('size'=>60,'maxlength'=>255)); ?>
            </div>
            <div class="form-group">
                <label for="name">Отображаемое имя</label>
                <?php echo $form->textField($model, 'name',array('size'=>60,'maxlength'=>255,'placeholder'=>'Имя')); ?>
            </div>
            <div class="form-group">
                <label for="byHtml">Отображать через:</label>
                <?php echo $form->dropDownList($model, 'byHtml',[" файл"," редактор ниже"],array()); ?>
            </div>
        </div>
        <div class="well">
            <div class="form-group">
                <label for="view">Файл отображения</label>
                <?php echo $form -> textField($model, "view"); ?>
            </div>
        </div>
        <div class="well">
            <div class="form-group">
                <label for="html">Текст</label>
                <?php
                $this->widget('application.extensions.tinymce.TinyMce',
                    array(
                        'model'=>$model,
                        'attribute'=>'html'
                    ));
                ?>
            </div>
        </div>
        <?php echo UHtml::submitButton($model->isNewRecord ? CHtml::encode('Создать') : CHtml::encode('Сохранить')); ?>
    </fieldset>
<?php $this -> endWidget(); ?>