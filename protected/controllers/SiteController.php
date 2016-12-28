<?php

class SiteController extends Controller
{
	public function onBeforeAction(){
		$u = Yii::app() -> user;
		return true;
	}
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
			'stem' => array(
				'class' => 'application.controllers.actions.FileViewAction',
				'view' => '//site/morph',
				'ignore' => true,
				'access' => true,
				'partial' => true
			),
			'deleteStem' => array(
				'class' => 'application.controllers.actions.FileViewAction',
				'view' => '//site/deleteStem',
				'ignore' => true,
				'access' => true,
				'partial' => true
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	public function actionCheck(){
		$t = new Text();
		$t -> text = "Санкт-Петербургская клиническая больница Российской Академии наук была основана в далеком 1962 году и изначально была создана как больница для сотрудников Академии. На сегодняшний же день это большая многопрофильная клиника, которая осуществляет работу по направлениям терапии, кардиологии, хирургии, диагностики и многим другим. Клиника на проспекте Мориса Тореза также содержит в своем составе центры, работающие с заболеваниями щитовидной и молочных желез, центр профилактики нарушений мозгового кровообращения, реабилитационный центр и прочие.
В числе медицинских услуг, которые предоставляет клиническая больница РАН, также присутствует лучевая, ультразвуковая и рентген-диагностика. Компьютерная томография в больнице Академии наук проводится в центре лучевой диагностики Медика, существующей на базе медцентра. Также в Медике проводится такое обследование, как денситометрия (оценка плотности костной ткани). Непосредственно же больницей РАН проводятся УЗИ, рентген и МРТ-диагностика.
МРТ на Тореза 72 делают на томографе Siemens Magnetom Essenza, напряженность магнитного поля в котором составляет 1,5 Тл. Данная модель позволяет проводить все стандартные клинические исследования и обеспечивает высокий уровень точности и качества результатов. Аппарат имеет закрытый тип. Помимо того, отделение также укомплектовано несколькими рентгеновскими аппаратами (Philips Tele Diagnost) и аппаратами ультразвуковой диагностики марки Hitachi.
Официальный сайт Санкт-Петербургской клинической больницы РАН обеспечивает всей востребованной информацией: на странице клиники можно ознакомиться с полным перечнем цен на платные услуги, найти контакты больницы либо заказать обратный звонок от представителя клиники РАН, ознакомиться с историей клиники и узнать многое другое. Недостатком страницы можно назвать неочевидное ее устройство при отсутствии карты сайта, что может несколько усложнить поиск информации.
В целом клиническая больница РАН - это современный медицинский центр, работающий в большинстве сфер медицины, укомплектованный грамотным персоналом и имеющий преимущественно неплохие отзывы о своей работе.";
		$t -> evalSickness();
	}
	public function actionAddAccounts () {
		for ($i = 225; $i <= 245 ; $i++) {
			$user = new User();
			$user -> setScenario('create');
			$user -> id = $i - 200;
			$user -> username = 'Test'.$i;
			$user -> name = $user -> username;
			$user -> id_type = 3;
			$user -> input_password_second = (2 + $i%10 + (floor(($i%100)/10))).'test';
			$user -> input_password = $user -> input_password_second;
			if (!$user -> save()) {
				var_dump($user -> getErrors());
				echo $i;
			}
			//var_dump($user);
			//break;
		}
	}
	public function actionCheckCopy() {
		$author = Author::model() -> findByPk(25);
		if ($author) {
			$task = Task::model() -> findByPk(46);
			$task -> copyToAuthor($author);
		}
	}
}