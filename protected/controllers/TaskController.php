<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 18:02
 */
class TaskController extends Controller {
    public function actions(){
        return array(
            'view' => array(
                'class' => 'application.controllers.actions.ModelViewAction',
                //'view' => function ($model) { return '//pattern/'.$model -> pattern -> view;},
                'view' => '//pattern/common',
                'ignore' => false,
                'modelClass' => 'Task',
                'scenario' => 'view'
            ),
            'makeTask' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'method' => 'lastText',
                'redirectMethod' => 'redirectAfterTextCreate',
                'ignore' => true,
                'modelClass' => 'Task',
                'scenario' => 'make',
                'access' => function($task){
                    return ($task -> author == User::logged());
                }
            ),
        );
    }
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