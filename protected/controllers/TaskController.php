<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 18:02
 */
class TaskController extends Controller {
    public $layout = 'cabinet';
    public function actions(){
        return array(
            /**'view' => array(
                'class' => 'application.controllers.actions.ModelViewAction',
                //'view' => function ($model) { return '//pattern/'.$model -> pattern -> view;},
                'view' => '//pattern/common',
                'ignore' => false,
                'modelClass' => 'Task',
                'scenario' => 'view'
            ),*/
            'view' => array(
                'class' => 'application.controllers.actions.ModelViewAction',
                //'view' => function ($model) { return '//pattern/'.$model -> pattern -> view;},
                'view' => '//task/_history',
                //'partial' => true,
                'ignore' => false,
                'modelClass' => 'Task',
                'scenario' => 'history'
            ),
            'makeTask' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'method' => 'prepareTextModel',
                'redirectMethod' => 'redirectAfterTextCreate',
                'ignore' => true,
                'modelClass' => 'Task',
                'scenario' => 'make',
                'access' => function($task){
                    return ($task -> author == User::logged());
                }
            ),
            'edit' => array(
                'class' => 'application.controllers.actions.ModelUpdateAction',
                'modelClass' => 'Task',
                'scenario' => 'generate',
                'view' => '//task/_form',
                'redirectUrl' => function($model){
                    /*if ($_POST['editTask']) {
                        return Yii::app() -> createUrl('task/edit',['arg' => $model -> id]);
                    } else {
                        return Yii::app() -> createUrl('cabinet/index');
                    }*/
                    return Yii::app() -> createUrl('cabinet/index');
                },
                'redirect' => false,
                'ignore' => false
            ),
            'move' => array(
                'class' => 'application.controllers.actions.ModelUpdateAction',
                //'view' => function ($model) { return '//pattern/'.$model -> pattern -> view;},
                //'partial' => true,
                'ignore' => true,
                'modelClass' => 'Task',
                'scenario' => 'move',
                'redirect' => '/cabinet'
            ),
        );
    }

    /**
     * Ajax action that returns children info by the given task id
     */
    public function actionChildren(){
        $data = $_GET;
        if ($data['id']) {
            $model = Task::model()->findByPk($data['id']);
            $models = $model -> children;
        } else {
            $models = array_merge(Task::model() -> root() -> findAll(), Task::model() -> uncategorized() -> findAll());
        }
        echo json_encode(UHtml::giveArrayFromModels($models,function($el){
            return array('id' => $el -> id, 'name' => $el -> name);
        }), JSON_PRETTY_PRINT);
    }
}