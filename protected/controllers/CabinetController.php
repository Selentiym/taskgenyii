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
    public function actionLog(){
        ob_start();
        var_dump($_GET);
        var_dump($_POST);
        var_dump($_REQUEST);
        $out = ob_get_contents();
        ob_end_clean();
        $f = fopen(Yii::getPathOfAlias('application'). DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . "log.txt",'w');
        fwrite($f, $out.'<br/><br/>'.PHP_EOL);
        fclose($f);
    }
    public function actionviewLog(){
        $filename = Yii::getPathOfAlias('application'). DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . "log.txt";
        if (file_exists($filename)) {
            echo file_get_contents($filename);
        } else {
            echo "No log for today yet.";
        }
    }
}