<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.07.2016
 * Time: 12:46
 */
class TinyMce extends CInputWidget {
    private $assetsDir;

    /** @var array Widget settings will override defaultSettings */
    public $settings = array();
    /** @var array default settings for tinymce */
    private static $defaultSettings = array(
        //'plugins' => array('responsivefilemanager', 'code'),
        "selector" =>"textarea",
        "theme" => "modern",
        "plugins" => [
             "advlist autolink link image lists charmap print preview hr anchor pagebreak",
             "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
             "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
        ],
        "toolbar1" => "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
        "toolbar2" => "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
        "image_advtab" => true ,
    );

    public function init(){

        $dir = dirname(__FILE__) . '/vendor/';
        $this->assetsDir = Yii::app()->assetManager->publish($dir);

        $this -> settings ["external_filemanager_path"] = Yii::app() -> baseUrl. '/vendor/filemanager/';
        if (empty($this -> settings ["external_plugins"])) {
            $this -> settings ["external_plugins"] = array();
        }
        $this -> settings ["external_plugins"] = array_merge($this -> settings ["external_plugins"], array(
            'filemanager' => $this -> settings ["external_filemanager_path"] . 'plugin.min.js'
        ));
        $this->settings['script_url'] = "{$this->assetsDir}/tinymce.min.js";
        $this->settings = array_merge(self::$defaultSettings, $this->settings);
    }
    public function run(){

        list($name, $id) = $this->resolveNameID();


        if (isset($this->htmlOptions['id']))
            $id = $this->htmlOptions['id'];
        else
            $this->htmlOptions['id'] = $id;
        if (isset($this->htmlOptions['name']))
            $name = $this->htmlOptions['name'];

        if (isset($this->model)) {
            echo CHtml::textArea($name, CHtml::resolveValue($this->model, $this->attribute), $this->htmlOptions);
        } else {
            echo CHtml::textArea($name, $this->value, $this->htmlOptions);
        }



        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
        $cs->registerScriptFile($this->assetsDir . '/tinymce.min.js');
        $cs->registerScriptFile($this->assetsDir . '/jquery.tinymce.min.js');

        $settings = CJavaScript::encode($this->settings);

        $cs->registerScript("{$id}_tinyMce_init", "$('#{$id}').tinymce({$settings});");
    }
}

