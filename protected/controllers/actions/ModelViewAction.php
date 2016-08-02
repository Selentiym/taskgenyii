<?php
	class ModelViewAction extends UAction {
		/**
		 * @var string $model class for action
		 */
		public $modelClass;

		/**
		 * @var string $view for render
		 */
		public $view = false;
		/**
		 * @var string $scenario -defines which scenario to use while searching
		 */
		public $scenario = 'search';
		/**
		 * @var boolean $partial - whether to user render partial.
		 */
		public $partial = false;
		/**
		 * @var bool $ajax - whether this is an ajax action
		 */
		public $ajax = false;
		/**
		 *
		 */
		public $layout = '//layouts/cabinet';
		/**
		 * @param $arg string model argument to be taken into customFind
		 * @throws CHttpException
		 */
		public function run($arg = false)
		{
			if (!Yii::app() -> user -> isGuest) {
				//Устанавливаем сценарий для модели поиска, чтобы искать правильным образом
				$model = CActiveRecord::model($this->modelClass);
				if ($this -> scenario) {
					$model -> setScenario($this -> scenario);
				}
				$model = $model->customFind($arg);
				//Если так ничего и не нашлось, возмущаемся
				if(!$model)
					throw new CHttpException(404, "{$this->modelClass} not found");
				//Устанавливаем шаблон
				$this->controller->layout = $this -> layout;
				//Вычисляем вьюху, если вообще функция задана
				if (is_callable($this -> view)) {
					$this -> view = call_user_func($this -> view,$model);
				}
				/** Работа с историей  */
				if (is_callable($this -> ignore)) {
					$this -> ignore = call_user_func($this -> ignore,$model);
				}
				/** Конец работы с историей */
				if ($this -> partial) {
					$this->controller->renderPartial($this->view, array('model' => $model, 'get' => $_GET), false, true);
				} else {
					$this->controller->render($this->view, array('model' => $model, 'get' => $_GET));
				}
			} else {
				$this -> controller -> redirect(Yii::app() -> baseUrl.'/login');
			}
		}
	}
?>