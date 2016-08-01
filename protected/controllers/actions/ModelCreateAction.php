<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 17:07
 */
class ModelCreateAction extends UAction {
    /**
     * @var string model class for action
     */
    public $modelClass;

    /**
     * @var string view for render
     */
    public $view;
    /**
     * @var string scenario - scenario that is to be assigned to the model
     */
    public $scenario = 'create';
    /**
     * @var string|array redirectUrl - the Url or an array to generate url where the user will be redirected after update
     */
    public $redirectUrl = array('/cabinet');
    /**
     * @var bool $partial - whether this is an ajax action
     */
    public $partial = false;

    /**
     * @param $arg bool|string  - the argument of customFind
     * @throws CHttpException
     */
    public function run($arg = false)
    {
        if (!Yii::app() -> user -> isGuest) {
            $modelName = $this -> modelClass;

            /**
             * @var UModel $model
             */
            //Создаем модель с нужным сценарием.
            $model = new $modelName($this -> scenario);
            //Даем модели знать, что она такое. (для юзера, например, определяем тип создаваемого юзера)
            $model -> readData($_GET);
            /** Работа с историей  */
            if (is_callable($this -> ignore)) {
                $this -> ignore = call_user_func($this -> ignore,$model);
            }
            /** Конец работы с историей */
            //echo "<br/>";
            //print_r($_POST);
            //Если у зашедшего достаточно прав, чтобы создать модель, то делаем это, иначе выводим сообщение, что юзер не прав.
            if ($model -> checkCreateAccess($arg)) {
                if ($_FILES) {
                    //Помимо обычных атрибутов устанавливаем файлы
                    $model -> fileOperations($_FILES);
                }
                $created = false;
                //Сохраняем атрибуты
                if (!empty($_POST[$this -> modelClass])) {

                    $model -> attributes = $_POST[$this -> modelClass];
                    if ($model -> save()) {
                        //uncomment$this->controller -> redirect($model -> redirectAfterCreate($this -> redirectUrl));
                        new CustomFlash('success',$this -> modelClass, 'CreateSuccess','Создание успешно!',true);
                        $created = true;
                        //echo "Saved!!!!";
                    } else {
                        print_r($model -> getErrors());
                        $model -> explainErrors();
                    }//*/
                }
                //В случае удачного создания перенаправляем
                if (($created)&&($this -> redirectUrl)){
                    $this -> controller -> redirect($this -> redirectUrl);
                }
                //$this->controller->layout = '//layouts/site';
                if (!$this -> partial) {
                    $this->controller->render($this->view, array('model' => $model));
                } else {
                    $this->controller->renderPartial($this->view, array('model' => $model));
                }
            } else {
                $this -> controller -> render('//accessDenied');
            }
            //}
        } else {
            $this -> controller -> redirect(Yii::app() -> baseUrl.'/login');
        }
    }
}
?>