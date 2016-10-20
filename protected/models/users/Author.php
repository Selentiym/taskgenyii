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

    public $idCreatedPay;

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
            'completedNotPayedTasks' => array(self::HAS_MANY, 'Task', 'id_author', 'condition' => 'id_text IS NOT NULL AND (SELECT COUNT(`id`) FROM `tbl_task_payment` `tp` WHERE `tp`.`id_task` = completedNotPayedTasks.`id`) = 0')
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
        $rez = reset(mysqli_fetch_row(mysqli_query(MysqlConnect::getConnection(),"SELECT SUM(`te`.`length`) FROM `tbl_task` `t` INNER JOIN `tbl_text` `te` ON (`te`.`id` = `t`.`id_text`) WHERE (`t`.`id_author` = '".$this -> id."') AND (SELECT COUNT(`id`) FROM `tbl_task_payment` `tp` WHERE `tp`.`id_task` = `t`.`id`) = 0")));
        return $rez > 0 ? $rez : 0;
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
        $s = $this -> getScenario();
        switch($s){
            case 'cabinet':
                return User::logged();
                break;
        }
        return self::model() -> findByPk($arg);
    }

    /**
     * Создает объект оплаты, добавляя в него все завершенные, но пока не оплаченные тексты.
     */
    public function pay(){
        $tasks = $this -> notPayedTasks();
        if (count($tasks) == 0) {
            Yii::app() -> user -> setFlash('noUnpayedTasks','Автор не имеет неоплаченных символов.');
            return;
        }
        $pay = new Payment();
        $pay -> id_author = $this -> id;
        $pay -> sum = $this -> CalculatePayment($tasks);
        if ($pay -> save()) {
            $this -> idCreatedPay = $pay -> id;
            //После создания объекта оплаты, добавляем в него оплаченные тексты.
            foreach ($tasks as $task) {
                $link = new TaskPayment();
                $link->id_task = $task->id;
                $link->id_payment = $pay->id;
                if ($link->save()) {
                    $task -> save();
                }
            }
            $this -> notify('В ближайшее время Вам должен поступить платеж на сумму '.$pay -> sum.'. <a href="'.Yii::app() -> createUrl('cabinet/payView',['arg' => $pay -> id]).'">Подробная информация</a>.');
        }
    }

    /**
     * @return int
     * @param Task[] $tasks - задания к оплате
     */
    public function CalculatePayment($tasks){
        $sum = array_reduce($tasks, function($task){
            return $task -> rezult -> length;
        }, 0);
        return round($sum / 1000 * 65);
    }
    public function redirectAfterPay(){
        if ($this -> idCreatedPay) {
            return Yii::app()->createUrl('cabinet/payView', ['arg' => $this->idCreatedPay]);
        }
        return Yii::app()->createUrl('cabinet/authorStat', ['arg' => $this->id]);
    }
    /*
     * @return int[] task ids that were accepted not from the first time
     */
    public function secondlyAcceptedIds() {
        return array_map(function($el){return (int)$el[0];},mysqli_fetch_all(mysqli_query($conn = MysqlConnect::getConnection(),"SELECT `tbl_task`.`id` FROM `tbl_task` WHERE (`tbl_task`.`id_author` = '".$this -> id."')  AND  `tbl_task`.`id_text` IS NOT NULL AND (SELECT COUNT(`id`) FROM `tbl_text` WHERE `tbl_text`.`id_task` = `tbl_task`.`id` AND `tbl_text`.`accepted` = '0') > 0")));
    }
    /**
     * @return Task[] tasks that were accepted not from the first time
     */
    public function secondlyAccepted(){
        return Task::model() -> findAllByPk($this -> secondlyAcceptedIds());
    }
    public function notPayedTasksIds(){
        return array_map(function($el){return (int)$el[0];},mysqli_fetch_all(mysqli_query($conn = MysqlConnect::getConnection(),"SELECT `t`.`id` FROM `tbl_task` `t` WHERE (`t`.`id_author` = '".$this -> id."')  AND  `t`.`id_text` IS NOT NULL AND (SELECT COUNT(`id`) FROM `tbl_task_payment` `tp` WHERE `tp`.`id_task` = `t`.`id`) = 0")));
    }
    public function notPayedTasks(){
        return Task::model() -> findAllByPk($this -> notPayedTasksIds());
    }
}