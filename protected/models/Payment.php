<?php

/**
 * This is the model class for table "{{payment}}".
 *
 * The followings are the available columns in table '{{payment}}':
 * @property integer $id
 * @property string $date
 * @property integer $confirmed
 * @property string $comment
 * @property integer $id_author
 * @property integer $sum
 *
 * relations:
 * @property Task[] $tasks
 * @property Task[] $tasks_not_marked
 */
class Payment extends UModel
 {
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{payment}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('id_author, sum', 'required'),
				array('confirmed, id_author, sum', 'numerical', 'integerOnly'=>true),
				array('date, comment', 'safe'),
				// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, confirmed, comment, id_author, sum', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'tasks' => array(self::MANY_MANY, 'Task', 'tbl_task_payment(id_payment, id_task)'),
			);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'id' => 'ID',
				'date' => 'Date',
				'confirmed' => 'Confirmed',
				'comment' => 'Comment',
				'id_author' => 'Id Author',
				'sum' => 'Sum',
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('confirmed',$this->confirmed);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('id_author',$this->id_author);
		$criteria->compare('sum',$this->sum);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

		/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Payment the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
