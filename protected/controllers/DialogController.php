<?php

class DialogController extends Controller {
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}*/

	public function actions() {
		// return external action classes, e.g.:
		return array(
            'send' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'User',
                'method' => 'sendJS',
                'guest' => false,
                'access' => true,
                'args' => $_POST,
                'ajax' => true,
                'ignore' => true,
                'scenario' => 'send'
            ),
            'history' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'User',
                'method' => 'dialogHistory',
                'guest' => false,
                'access' => true,
                'args' => $_POST,
                'ajax' => true,
                'ignore' => true,
                'scenario' => 'dialogHistory'
            ),
            'checkNewLetters' => array(
                'class' => 'application.controllers.actions.ClassMethodAction',
                'modelClass' => 'User',
                'method' => 'checkNewLetters',
                'guest' => false,
                'access' => true,
                'args' => $_POST,
                'ajax' => true,
                'ignore' => true,
                'scenario' => 'checkNewLetters'
            ),
			'read' => array(
				'class' => 'application.controllers.actions.ClassMethodAction',
				'modelClass' => 'User',
				'method' => 'read',
				'guest' => false,
				'access' => true,
				'args' => $_POST,
				'ajax' => true,
				'ignore' => true,
				'scenario' => 'read'
			),
		);
	}
}