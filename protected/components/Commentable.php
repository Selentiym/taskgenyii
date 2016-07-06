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
    public function relations() {
        return array(
            'comments' => array(self::HAS_MANY,'BaseCall',
                'id_user', 'condition' => 'id_obj_type = ' . $this -> CommentId()),
        );
    }

    /**
     * @return int - identificator of the object type for the comments table
     */
    abstract protected function CommentId();
}