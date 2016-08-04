<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 10:27
 */

/**
 * The followings are the available model relations:
 * @property Task[] $tasks
 * @property Task[] $activeTasks
 * @property Task[] $completedTasks
 */
class Author extends User {
    public function view(){
        return 'author';
    }
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function relations(){
        return parent::relations() + array(
            'tasks' => array(self::HAS_MANY, 'Task', 'id_author'),
            'activeTasks' => array(self::HAS_MANY, 'Task', 'id_author', 'condition' => 'id_text IS NULL'),
            'completedTasks' => array(self::HAS_MANY, 'Task', 'id_author', 'condition' => 'id_text IS NOT NULL'),
        );
    }
}