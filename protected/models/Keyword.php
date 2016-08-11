<?php

/**
 * This is the model class for table "{{keyword}}".
 *
 * The followings are the available columns in table '{{keyword}}':
 * @property integer $id
 * @property integer $id_task
 * @property integer $num
 * @property string $word
 *
 * The followings are the available model relations:
 * @property Task $idTask
 */
class Keyword extends StringModel{
	public $truncatedWord;
	/**
	 * @return string - name of the attribute
	 */
	public function stringAttribute() {
		$pos = strpos($this -> word,',');
		if ($pos > 1) {
			$this->truncatedWord = substr($this->word, 0, $pos);
		} else {
			$this->truncatedWord = $this->word;
		}
		return 'truncatedWord';
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{keyword}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('id_task, word', 'required'),
				array('id_task, num', 'numerical', 'integerOnly'=>true),
				array('word', 'length', 'max'=>512),
				// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_task, num, word', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'idTask' => array(self::BELONGS_TO, 'Task', 'id_task'),
			);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'id' => 'ID',
				'id_task' => 'Id Task',
				'num' => 'Num',
				'word' => 'Word',
			);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search() {
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

			$criteria->compare('id',$this->id);
		$criteria->compare('id_task',$this->id_task);
		$criteria->compare('num',$this->num);
		$criteria->compare('word',$this->word,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

		/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Keyword the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	public function giveShortcut() {
		if (strpos($this -> word, ',') > 0) {
			return substr($this->word, 0, strpos($this->word, ','));
		}
		return $this -> word;
	}
}
