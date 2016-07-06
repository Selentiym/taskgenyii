<?php

/**
 * This is the model class for table "{{text}}".
 *
 * The followings are the available columns in table '{{text}}':
 * @property integer $id
 * @property integer $id_task
 * @property integer $length
 * @property string $text
 *
 * The followings are the available model relations:
 * @property Task $idTask
 */
class Text extends Commentable {
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{text}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('id, id_task', 'required'),
				array('id, id_task, length', 'numerical', 'integerOnly'=>true),
				array('text', 'safe'),
				// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_task, length, text', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @inherited
	 */
	public function CommentId() {
		return 2;
	}
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'idTask' => array(self::BELONGS_TO, 'Task', 'id_task'),
			) + parent::relations();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'id' => 'ID',
				'id_task' => 'Id Task',
				'length' => 'Length',
				'text' => 'Text',
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
		$criteria->compare('length',$this->length);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

		/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Text the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
