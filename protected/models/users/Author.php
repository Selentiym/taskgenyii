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
 * @property Task[] $notPayedTasks
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
            'completedTasksNum' => array(self::STAT, 'Task', 'id_author', 'condition' => 'id_text IS NOT NULL'),
            'notPayedTasks' => array(self::HAS_MANY, 'Task', 'id_author', 'condition' => 'id_text IS NOT NULL AND `payed`=0'),
            'secondlyAccepted' => array(self::HAS_MANY, 'Task', 'id_author', 'condition' => 'id_text IS NOT NULL', 'having'=>'COUNT(`notAcceptedTexts`.`id`) > 0','with' => ['notAcceptedTexts']),
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
     * Возвращает количество символов, которые одобрены, но не оплачены.
     * @return int
     * @throws DatabaseException
     */
    public function symbolsNotPayed(){
        /*$sum = 0;
        foreach ($this -> notPayedTasks as $task) {
            $sum += $task -> rezult -> length;
        }
        return $sum;*/
        return reset(mysqli_fetch_assoc(mysqli_query($conn = MysqlConnect::getConnection(),"SELECT SUM(`tbl_text`.`length`) FROM `tbl_text`, `tbl_task` WHERE `tbl_text`.`id` = `tbl_task`.`id_text` AND `tbl_text`.`accepted` = '1' AND `tbl_task`.`id_author` = '$this->id' AND `tbl_task`.`payed`='0'")));
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
    public function customFind($arg){
        return self::model() -> findByPk($arg);
    }

    /**
     * Создает объект оплаты, добавляя в него все завершенные, но пока не оплаченные тексты.
     */
    public function pay(){
        $tasks = $this -> notPayedTasks;
        if (count($tasks) == 0) {
            return;
        }
        $pay = new Payment();
        $pay -> id_author = $this -> id;
        $pay -> sum = $this -> CalculatePayment($tasks);
        if ($pay -> save()) {
            //После создания объекта оплаты, добавляем в него оплаченные тексты.
            foreach ($tasks as $task) {
                $link = new TaskPayment();
                $link->id_task = $task->id;
                $link->id_payment = $pay->id;
                $link->save();
            }
            $this -> notify('В ближайшее время Вам должен поступить платеж на сумму '.$pay -> sum.'. <a href="'.Yii::app() -> createUrl('payment/view',['arg' => $pay -> id]).'">Подробная информация</a>.');
        }
    }

    /**
     * @return int
     * @param Task[] $tasks - задания к оплате
     */
    public function CalculatePayment($tasks){
        $sum = array_reduce($tasks, function($text){
            return $text -> length;
        }, 0);
        return round($sum / 1000 * 65);
    }
}