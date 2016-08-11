<?php

/**
 * This is the model class for table "{{search_phrase}}".
 *
 * The followings are the available columns in table '{{search_phrase}}':
 * @property integer $id
 * @property string $phrase
 * @property integer $id_task
 * @property integer $baseFreq
 * @property integer $phraseFreq
 * @property integer $directFreq
 */
class SearchPhrase extends StringModel {
	/**
	 * @var bool
	 */
	private $idOldTask = false;
	/**
	 * @return string - name of the attribute
	 */
	public function stringAttribute() {
		return 'phrase';
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{search_phrase}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('phrase, id_task', 'required'),
				array('id_task', 'numerical', 'integerOnly'=>true),
				array('phrase', 'length', 'max'=>512),
				// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, phrase, id_task', 'safe', 'on'=>'search'),
			array('baseFreq, directFreq, phraseFreq, phrase, id_task', 'safe', 'on'=>'create'),
			array('id', 'unsafe', 'on'=>'create'),
			array('*', 'unsafe', 'on'=>'move'),
			array('id_task', 'safe', 'on'=>'move'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'id' => 'ID',
				'phrase' => 'Phrase',
				'id_task' => 'Id Task',
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
	 * @return CDbCriteria
	 */
	public function search() {
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('phrase',$this->phrase,true);
		$criteria->compare('id_task',$this->id_task);
		if ($this -> baseFreq) {
			$criteria->compare('baseFreq', $this->baseFreq);
		}
		if ($this -> directFreq) {
			$criteria->compare('directFreq', $this->directFreq);
		}
		if ($this -> phraseFreq) {
			$criteria->compare('phraseFreq', $this->phraseFreq);
		}

		return $criteria;
	}

		/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SearchPhrase the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	protected function beforeSave(){
		if ($this -> scenario == 'move') {
			$this -> id_task = $_GET['where'];
		}
		//Не даем добавлять уже существующие фразы
		if ($this -> find($this -> search())) {
			$this -> addError('id','Duplicate phrase. There is a phrase corresponding to the current task that has the same parameters.');
			return false;
		}
		if (!$this -> isNewRecord) {
			$db = $this -> DBModel();
			if ($db -> id_task != $this -> id_task) {
				$this -> idOldTask = $db -> id_task;
			}
		}
		return true;
	}
	protected function afterSave() {
		if ($this -> idOldTask) {
			$task = Task::model() -> findByPk($this -> id_task);
			$task -> renewVocabularyWithSearchPhrase($this);
			$oldTask = Task::model() -> findByPk($this -> idOldTask);
			$oldTask -> renewVocabularyRemoveSearchPhrase($this);
		}
		parent::afterSave();
	}
}
