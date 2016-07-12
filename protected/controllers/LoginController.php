<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.07.2016
 * Time: 16:25
 */
class LoginController extends Controller
{
    public $loginFormClass = 'ULoginForm';
    public $defaultAction = 'login';
    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $modelClass = $this -> loginFormClass;
        $model=new $modelClass();

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='loginForm')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if (!Yii::app() -> user -> isGuest) {
            $this -> redirect(Yii::app()->baseUrl);
        }
        // collect user input data
        if(isset($_POST[get_class($model)]))
        {
            $model->attributes=$_POST[get_class($model)];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login())
                $this->redirect(Yii::app()->baseUrl.'/');
        }
        // display the login form
        $this->renderPartial('login',array('model'=>$model));
    }
    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        Yii::app() -> user -> raiseEvent('onLogout',new CEvent($this));
        $this->redirect(Yii::app()->homeUrl);
    }
    /*public function actionpss(){
        echo CPasswordHelper::hashPassword('qwerty');
    }*/
    /*public function actionAddRules() {
        //print_r(Yii::app() -> getAuthManager());
        $auth = Yii::app()->authManager;
        $auth->clearAll();

        //$isParent = 'return Yii::app() -> user -> getId()==$params["user"] -> id_parent';
        //$auth->createOperation('viewChildUserCabinet', 'view your child user\'s cabinet.', $isParent);

        $admin = $auth->createRole('admin');
        $editor = $auth->createRole('editor');
        $author = $auth->createRole('author');

        $editor->addChild('author');
        $admin->addChild('editor');

        $this->AddAdminUser();
        //$auth -> createOperation('viewOwnUserCabinet', 'view your own cabinet.', $bizRule);
    }

    public function AddAdminUser() {
        $auth = Yii::app()->authManager;
        $admin = User::model()->findByAttributes(array('username' => 'shubinsa'));
        $editor = User::model()->findByAttributes(array('username' => 'nikita'));
        $author1 = User::model()->findByAttributes(array('username' => 'anna'));
        $author2 = User::model()->findByAttributes(array('username' => 'nastya'));

        $auth->assign('admin', $admin->id);
        $auth->assign('editor', $editor->id);
        $auth->assign('author', $author1->id);
        $auth->assign('author', $author2->id);
    }*/
}