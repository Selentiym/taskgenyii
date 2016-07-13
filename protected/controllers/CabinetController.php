<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 9:29
 */
class CabinetController extends Controller {
    public $defaultAction = 'authenticate';
    public $layout = 'cabinet';

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            'index' => array(
                'class' => 'application.controllers.actions.ModelViewAction',
                'modelClass' => 'User',
                'scenario' => 'cabinet',
                'view' => function($user){return $user -> view();},
                'layout' => 'cabinet'
            ),

            'TaskCreate' => array(
                'class' => 'application.controllers.actions.ModelCreateAction',
                'modelClass' => 'Task',
                'view' => '//task/create',
                'scenario' => 'create'
            ),
            'loadKeywords' => array(
                'class' => 'application.controllers.actions.FileViewAction',
                'view' => '//task/keyform',
                'ignore' => true,
                'access' => true
            ),
        );
    }


    public function actionAuthenticate(){
        //Если не залогинен, то все сделается правильно
        $this -> redirect(Yii::app() -> baseUrl.'/cabinet');
    }
    public function actionBack(){
        $this -> back();
    }
    public function actionCheck() {
        $uid = TextRuApiHelper::addPost(
            'МРТ в Красносельском районе СПБ можно пройти в нескольких клиниках, нужно только выбрать ту, которая будет удобней в каждом конкретном случае – каждая из них имеет свои преимущества перед другими. В данном районе города располагаются в основном государственные медцентры, оказывающие подобные услуги (в отличие от других, к примеру, Кировского района), что имеет как свои плюсы, так и минусы.'
        );
        $uid = '5786014f8c660';
        if ($uid) {
            $check = TextRuApiHelper::getResultPost($uid);
            sleep(10);
            $check = TextRuApiHelper::getResultPost($uid);
        }
    }
}