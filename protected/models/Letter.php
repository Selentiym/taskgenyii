<?php

/**
 * This is the model class for table "{{letters}}".
 *
 * The followings are the available columns in table '{{letters}}':
 * @property integer $id
 * @property integer $id_sender
 * @property integer $id_receiver
 * @property string $text
 * @property string $sent
 * @property integer $opened
 */
class Letter extends UModel{
	const SYSTEM_SENDER = 0;
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{letters}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('id_sender, id_receiver', 'required'),
				array('id_sender, id_receiver, opened', 'numerical', 'integerOnly'=>true),
				array('meassage', 'safe'),
				// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_sender, id_receiver, meassage, sent, opened', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'sender' => array(self::BELONGS_TO, 'User', 'id_sender'),
				'receiver' => array(self::BELONGS_TO, 'User', 'id_receiver'),
			);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'id' => 'ID',
				'id_sender' => 'Id Sender',
				'id_receiver' => 'Id Receiver',
				'meassage' => 'Meassage',
				'sent' => 'Sent',
				'opened' => 'Opened',
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
		$criteria->compare('id_sender',$this->id_sender);
		$criteria->compare('id_receiver',$this->id_receiver);
		$criteria->compare('meassage',$this->meassage,true);
		$criteria->compare('sent',$this->sent,true);
		$criteria->compare('opened',$this->opened);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

		/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Letter the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
