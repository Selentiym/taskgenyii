<?php
class UModel extends CActiveRecord {
    public function CustomFind($arg = false){
        return User::model() -> findByPk($arg);
    }
    /**
     * @return bool whether one can create this record or not
     */
    public function checkCreateAccess(){
        return true;
    }
    /**
     * @return bool whether one can update $this record or not
     */
    public function checkUpdateAccess(){
        return true;
    }
    /**
     * @return bool whether one can delete $this record or not
     */
    public function checkDeleteAccess(){
        return true;
    }
    /**
     * Sets CustomFlash with information about errors
     */
    public function explainErrors(){
        return;
    }
    /**
     * Функция, в которой следует описать все операции, производимые с файлами при
     * создании/изменении модели.
     * @param $files_arr - $_FILES array
     */
    public function fileOperations($files_arr) { return; }

    /**
     * @param mixed[] $data - некоторая информация со страницы
     */
    public function readData($data) { return; }

    /**
     * @return static - model stored in the database no matter what attributes
     * (only PK is sufficient) the model has now.
     */
    public function DBModel(){
        return $this -> findByPk($this -> getPrimaryKey());
    }
}
?>