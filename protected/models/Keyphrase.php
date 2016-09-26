<?php

/**
 * This is the model class for table "{{keyphrase}}".
 *
 * The followings are the available columns in table '{{keyphrase}}':
 * @property integer $id
 * @property integer $id_task
 * @property integer $direct
 * @property integer $morph
 * @property integer $freq
 * @property string $tag
 * @property string $phrase
 *
 * The followings are the available model relations:
 * @property Task $idTask
 */
class Keyphrase extends StringModel {
	public $meaningfulWordsCount;
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{keyphrase}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('id_task, phrase', 'required'),
				array('id, id_task, direct, morph', 'numerical', 'integerOnly'=>true),
				array('phrase', 'length', 'max'=>256),
				// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_task, direct, morph, phrase', 'safe', 'on'=>'search'),
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
				'direct' => 'Direct',
				'morph' => 'Morph',
				'phrase' => 'Phrase',
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
		$criteria->compare('direct',$this->direct);
		$criteria->compare('morph',$this->morph);
		$criteria->compare('phrase',$this->phrase,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

		/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Keyphrase the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string - name of the attribute
	 */
	public function stringAttribute() {
		return 'phrase';
	}

	/**
	 * @return integer how many non-stop words there are in the phrase
	 */
	public function getMeaningfulWordsCount(){
		if (!$this -> meaningfulWordsCount) {
			$this -> meaningfulWordsCount = count(array_filter($this -> giveStems()));;
		}
		return $this -> meaningfulWordsCount;
	}
}
