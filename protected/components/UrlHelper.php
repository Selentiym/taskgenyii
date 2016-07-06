<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 9:07
 */
class UrlHelper extends CApplicationComponent{
    /**
     * @var string[] $_paths contains the path history
     */
    private $_paths = array();
    /**
     * @var bool $_ignored - flags whether the current path was ignored
     */
    private $_ignored = false;
    /**
     * @var bool $_added flags whether the current path was added to history
     */
    private $_added = false;
    public function init() {
        $this -> _paths = Yii::app() -> session -> get('urls');
        if (empty($this -> _paths)) {
            $this -> _paths = array();
        }
    }
    /**
     * @param CHttpRequest $request
     */
    public function process($request){
        $path = $request -> getPathInfo();
        if (strpos($path,'.') === false) {
            $this->_added = false;
            if ($this->last() != $path) {
                $this->push($path);
                $this->_added = true;
            }
        }
    }

    /**
     * @return string the last saved path
     */
    public function last() {
        return end($this -> _paths);
    }

    /**
     * @param string $path path to be added to history
     */
    public function push($path) {
        $this -> _paths[] = $path;
        if (count($this -> _paths) > 10) {
            array_shift($this -> _paths);
        }
        $this -> save();
    }

    /**
     * @return string last unique path (do not count page refresh)
     */
    public function back(){
        $rez = array_pop($this -> _paths);
        if (!$this -> _ignored) {
            //Еще на шаг назад, потому что текущая страница была записана
            $rez = array_pop($this -> _paths);
        }
        $this -> save();
        return $rez;
    }
    public function paths(){
        return $this -> _paths;
    }
    public function save(){
        //Сохраняем историю
        Yii::app() -> session -> add('urls',$this -> _paths);
    }
    public function ignore(){
        if ($this -> _added) {
            //На шаг назад
            $this -> back();
            $this -> _ignored = true;
        }
    }
    public function clear(){
        $this -> _paths = array();
        $this -> save();
    }
    public function onLogout(CEvent $event) {
        $this -> clear();
    }
}