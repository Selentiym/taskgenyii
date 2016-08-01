<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 16:13
 */

/**
 * Class Commentable
 * The followings are the available model relations:
 * @property Comment[] $comments
 */
abstract class Commentable extends UModel {
    public static $types = array(
        1 => 'Task',
        2 => 'Text'
    );
    public function relations() {
        return array(
            'comments' => array(self::HAS_MANY,'Comment',
                'id_obj', 'condition' => 'id_obj_type = ' . $this -> CommentId(), 'order' => 'date DESC'),
        );
    }

    /**
     * @return int - identificator of the object type for the comments table
     */
    abstract public function CommentId();

    /**
     * @return string|array a correct alias for some view file
     */
    public function CommentView(){
        return '//comment/_default';
    }
}