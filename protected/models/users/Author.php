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

    /**
     * Возвращает количество символов, которые одобрены.
     * @return int
     * @throws DatabaseException
     */
    public function symbols(){
        return reset(mysqli_fetch_assoc(mysqli_query($conn = MysqlConnect::getConnection(),"SELECT SUM(`tbl_text`.`length`) FROM `tbl_text`, `tbl_task` WHERE `tbl_text`.`id` = `tbl_task`.`id_text` AND `tbl_text`.`accepted` = '1' AND `tbl_task`.`id_author` = '$this->id'")));
    }

    /**
     * @return bool
     */
    protected function beforeSave() {
        if (!$this -> id_type) {
            $this->id_type = UserType::getIdByStr($this->view());
        }
        $scenario = $this -> getScenario();

        switch($scenario) {
            case 'create':

                break;
        }
        return parent::beforeSave();
    }
}