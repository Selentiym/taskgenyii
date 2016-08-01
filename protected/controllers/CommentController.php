<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 01.08.2016
 * Time: 15:55
 */
class CommentController extends Controller{
    public $defaultLayout = 'cabinet';

    public function actions() {
        return array(
            'createComment' => array(
                'class' => 'application.controllers.actions.ModelCreateAction',
                'redirectUrl' => false,
                'ignore' => true,
                'modelClass' => 'Comment',
                'scenario' => 'create',
                'partial' => true,
                'view' => '//comment/create'
            ),
        );
    }
}