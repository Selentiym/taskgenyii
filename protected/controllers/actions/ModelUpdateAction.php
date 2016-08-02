<?php
	class ModelUpdateAction extends UAction
	{
		/**
		 * @var string model class for action
		 */
		public $modelClass;

		/**
		 * @var string view for render
		 */
		public $view;
		/**
		 * @var string|callable redirect - address from where to redirect to FORCEFULLY! Without view
		 */
		public $redirect;
		/**
		 * @var string|array|callable
		 */
		public $redirectUrl;
		/**
		 * @var string scenario - scenario that is to be assigned to the model
		 */
		public $scenario = false;
		/**
		 * @param mixed $arg  something that may identify a model
		 * @throws CHttpException
		 */
		public function run($arg = false)
		{
			if (!Yii::app() -> user -> isGuest) {
				//Чтобы поиск прошел с нужным сценарием.
				$toSearch = CActiveRecord::model($this->modelClass);
				if ($this -> scenario) {
					$toSearch -> setScenario($this -> scenario);
				}
				//Получаем модель, которую нужно обновить
				$model = $toSearch->customFind($arg);
				//Если не получилось ее найти, то сообщаем об ошибке
				if(!$model)
					throw new CHttpException(404, "{$this->modelClass} not found");
				/** Работа с историей  */
				if (is_callable($this -> ignore)) {
					$this -> ignore = call_user_func($this -> ignore,$model);
				}
				/** Конец работы с историей */
				if (is_callable($this -> redirect)) {
					$this -> redirect = call_user_func($this -> redirect, $model);
				}
				//Если у зашедшего достаточно прав, чтобы редактировать модель, то делаем это, иначе выводим сообщение, что юзер не прав.
				if ($model -> checkUpdateAccess()) {
					//Если указан, какой должен быть у модели сценарий, то задаем его.
					if ($this -> scenario) {
						$model -> setScenario($this -> scenario);
					}
					//Сохраняем атрибуты
					if (isset($_POST[$this -> modelClass])) {
						$model -> attributes = $_POST[$this -> modelClass];
						//$model -> setAttr($_POST);
						//var_dump($model);
						
						if ($model -> save()) {
							if (is_callable($this -> redirectUrl)) {
								$this -> redirectUrl = call_user_func($this -> redirectUrl, $model);
							}
							if ($this -> redirectUrl) {
								$this->controller->redirect($this->redirectUrl);
								new CustomFlash('success', $this->modelClass, 'CreateSuccess', 'Редактирование успешно!', true);
							}
						}				
					}
					if (!$this -> redirect) {
						//$this->controller->layout = '//layouts/site';
						$this->controller->render($this->view, array('model' => $model));
					} else {
						$this -> controller -> redirect(Yii::app() -> baseUrl . $this -> redirect);
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