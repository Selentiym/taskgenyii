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
            'rename' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'method' => 'renameJS',
                'ignore' => true,
                'modelClass' => 'Task',
                'scenario' => 'renameJS',
                'ajax' => true,
                'access' => function($task){
                    return Yii::app() -> user -> checkAccess('editor');
                }
            ),
            'editComment' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'method' => 'editComment',
                'ignore' => true,
                'modelClass' => 'Task',
                'scenario' => 'editComment',
                'ajax' => true,
                'access' => function($task){
                    return Yii::app() -> user -> checkAccess('editor');
                }
            ),
            'assignAuthor' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'method' => 'assignAuthor',
                'ignore' => true,
                'modelClass' => 'Task',
                'scenario' => 'assignAuthor',
                'ajax' => true,
                'access' => function($task){
                    return Yii::app() -> user -> checkAccess('editor');
                }
            ),
            'copyToAuthor' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'method' => 'copyToAuthorJS',
                'ignore' => true,
                'modelClass' => 'Task',
                'scenario' => 'copyToAuthor',
                'ajax' => true,
                'access' => function($task){
                    return Yii::app() -> user -> checkAccess('editor');
                }
            ),
            'createFast' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'method' => 'createDescendantFast',
                'ajax' => true,
                'ignore' => true,
                'modelClass' => 'Task',
                'scenario' => 'createDescendant',
                'access' => true
            ),
            'deleteGroup' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'Task',
                'method' => 'deleteGroup',
                'ignore' => true,
                'ajax' => true,
                'args' => $_POST,
                'scenario' => 'deleteGroup',
                'access' => function(){
                    return Yii::app() -> user -> checkAccess('editor');
                }
            ),
            'deleteKeys' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'Task',
                'method' => 'deleteKeys',
                'ignore' => true,
                'ajax' => true,
                'scenario' => 'deleteKeys',
                'access' => function(){
                    return Yii::app() -> user -> checkAccess('editor');
                }
            ),
            'edit' => array(
                'class' => 'application.controllers.actions.ModelUpdateAction',
                'modelClass' => 'Task',
                'scenario' => 'generate',
                'view' => '//task/_form',
                'redirectUrl' => function($model){
                    if ($_REQUEST['redirectToKeywords']) {
                        return Yii::app() -> createUrl('cabinet/loadKeywords',['arg' => $model -> id]);
                    }
                    if ($_REQUEST['redirectToShowTask']) {
                        return Yii::app() -> createUrl('task/view',['arg' => $model -> id]);
                    }
                    if ($_REQUEST['redirectToEditTask']) {
                        return Yii::app() -> createUrl('task/edit',['arg' => $model -> id]);
                    }
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
            'moveSearchPhrase' => array(
                'class' => 'application.controllers.actions.ModelUpdateAction',
                //'view' => function ($model) { return '//pattern/'.$model -> pattern -> view;},
                //'partial' => true,
                'ignore' => true,
                'modelClass' => 'SearchPhrase',
                'scenario' => 'move',
                'ajax' => true
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
            /**
             * @type Task $el
             */
            return $el -> dumpForProject();
        }), JSON_PRETTY_PRINT);
    }
    public function actionRecalculatePayments() {
        $tasks = Task::model() -> findAll('toPay IS NULL');
        foreach($tasks as $t) {
            /**
             * @type Task $t
             */
            $p = $t -> calculatePayment();
            $t -> toPay = $p;
            $t -> save(false,['toPay']);
        }
    }
}