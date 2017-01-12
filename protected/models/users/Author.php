<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 10:27
 */

/**
 * @property integer $tax
 * @property integer $prepayed
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
            'completedTasks' => array(self::HAS_MANY, 'Task', 'id_author', 'condition' => 'id_text IS NOT NULL', 'order' => 'created DESC'),
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
        $pay -> prepayWas = $this -> prepayed;
        $pay -> sum = $this -> CalculatePayment($tasks);
        $remainsPrepayed = -1;
        if ($pay -> sum < 0) {
            $remainsPrepayed = - $pay -> sum;
            $pay -> sum = 0;
        }
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
            if ($remainsPrepayed < 0) {
                $this->notify('В ближайшее время Вам должен поступить платеж на сумму ' . $pay->sum . ' руб. <a href="' . Yii::app()->createUrl('cabinet/payView', ['arg' => $pay->id]) . '">Подробная информация</a>. Была учтена предоплата в размере '. $pay -> prepayWas.' руб.');
                $this -> prepayed = 0;
            } else {
                $this -> notify('<a href="' . Yii::app()->createUrl('cabinet/payView', ['arg' => $pay->id]) . '">Некоторые задания</a> были оплачены засчет предоплаты. Осталось предоплаты '.$remainsPrepayed.' руб.');
                $this -> prepayed = $remainsPrepayed;
            }

            if (!$this -> save(false, ['prepayed'])) {
                throw new Exception("Не удалось поменять предоплату!");
            }
        }
    }
    public function prePay() {
        $data = $_POST;
        if (($data["goPrepay"])&&($data["prepay"])) {
            $was = $this -> prepayed;
            $toPay = $data["prepay"];
            $toBe = $toPay + $was;
            $this -> prepayed = $toBe;
            if ($this -> save(false, ['prepayed'])) {
                Yii::app() -> user -> setFlash('prepay',"Предоплата в размере $toPay руб начислена автору ".$this -> show().".");
                $this->notify("В ближайшее время Вам должна прийти предоплата в размере $toPay руб. Уже было $was руб, в сумме $toBe руб.");
            } else {
                Yii::app() -> user -> setFlash('prepay',"Не удалось начислить предоплату! Попробуйте еще раз.");
            }
        }
    }
    public function redirectToStat() {
        return Yii::app() -> createUrl('cabinet/authorStat',['arg' => $this -> id]);
    }
    /**
     * @return int
     * @param Task[] $tasks - задания к оплате
     */
    public function CalculatePayment($tasks){
        $sum = 0;
        foreach ($tasks as $t) {
            $sum += $t -> toPay;
        }
        return ($sum - $this -> prepayed);
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

    /**
     * @return bool
     */
    public function editTax() {
        $data = $_POST;
        if (($data["Author"])&&($data["tax"])) {
            $was = $this -> tax;
            $toBe = $data["tax"];
            $this -> tax = $toBe;
            if ($this -> save(false, ['tax'])) {
                Yii::app() -> user -> setFlash('tax',"Тариф автору ".$this -> show()." изменен с $was руб на $toBe руб.");
                $this->notify("Вам был изменен тариф с $was руб на $toBe руб. Он будет применен ко всем заданиям, принятым позже этого момента, даже если выдавались они при другом тарифе.");
                return true;
            }
        }
        Yii::app() -> user -> setFlash('tax',"Не удалось изменить тариф! Попробуйте еще раз.");
        return false;
    }
}