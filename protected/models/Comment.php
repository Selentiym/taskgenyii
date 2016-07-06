<?php

/**
 * This is the model class for table "{{comment}}".
 *
 * The followings are the available columns in table '{{comment}}':
 * @property integer $id
 * @property integer $id_user
 * @property integer $id_obj
 * @property integer $id_obj_type
 * @property string $comment
 * @property string $date
 */
class Comment extends UModel
 {
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{comment}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('id, id_user, id_obj, id_obj_type, comment', 'required'),
				array('id, id_user, id_obj, id_obj_type', 'numerical', 'integerOnly'=>true),
				// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_user, id_obj, id_obj_type, comment', 'safe', 'on'=>'search'),
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
				'id_user' => 'Id User',
				'id_obj' => 'Id Obj',
				'id_obj_type' => 'Id Obj Type',
				'comment' => 'Comment',
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
		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('id_obj',$this->id_obj);
		$criteria->compare('id_obj_type',$this->id_obj_type);
		$criteria->compare('comment',$this->comment,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

		/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Comment the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
