<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.07.2016
 * Time: 12:19
 */
class TextController extends Controller {
    public $defaultLayout = 'cabinet';
    public function actions(){
        return array(
            'write' => array(
                'class' => 'application.controllers.actions.ModelViewAction',
                'view' => '//text/write',
                'ignore' => false,
                'modelClass' => 'Text',
                'scenario' => 'write',
                'layout' => 'cabinet'
            ),
            'save' => array(
                'class' => 'application.controllers.actions.ModelUpdateAction',
                'modelClass' => 'Text',
                'scenario' => 'delay',
                'redirect' => '/cabinet',
                'ignore' => false
            ),
            'handIn' => array(
                'class' => 'application.controllers.actions.ModelUpdateAction',
                'modelClass' => 'Text',
                'scenario' => 'handIn',
                'redirectUrl' => Yii::app() -> baseUrl.'/cabinet',
                'redirect' => function($model){ return '/text/write/'.$model -> id; },
                'ignore' => true
            ),
            'handInWithMistakes' => array(
                'class' => 'application.controllers.actions.ModelUpdateAction',
                'modelClass' => 'Text',
                'scenario' => 'handInWithMistakes',
                'redirectUrl' => Yii::app() -> baseUrl.'/cabinet',
                'redirect' => function($model){ return '/text/write/'.$model -> id; },
                'ignore' => true
            ),
            'analyze' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'Text',
                'method' => 'analyze',
                'access' => true,
                'args' => $_POST,
                'ajax' => true,
                'ignore' => true,
                'scenario' => 'analyze'
            ),
            'seoStat' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'Text',
                'method' => 'seoStat',
                'access' => true,
                'args' => $_POST,
                'ajax' => true,
                'ignore' => true,
                'scenario' => 'analyze'
            ),
            'uniqueCheck' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'Text',
                'method' => 'addUnique',
                'access' => true,
                'args' => $_POST,
                'ajax' => true,
                'ignore' => true,
                'scenario' => 'analyze'
            ),
            'giveUnique' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'Text',
                'method' => 'giveUnique',
                'access' => true,
                'args' => true,
                'ajax' => true,
                'ignore' => true,
                'scenario' => 'analyze'
            ),
            'uniqueResult' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'Text',
                'method' => 'uniqueResult',
                'guest' => true,
                'args' => $_POST,
                'ajax' => true,
                'ignore' => true,
                'scenario' => 'analyze'
            ),
        );
    }
}