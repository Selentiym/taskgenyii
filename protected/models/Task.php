<?php

/**
 * This is the model class for table "{{task}}".
 *
 * The followings are the available columns in table '{{task}}':
 * @property integer $id
 * @property integer $id_author
 * @property integer $id_editor
 * @property integer $id_pattern
 * @property string $created
 * @property integer $id_text
 *
 * The followings are the available model relations:
 * @property Keyphrase[] $keyphrases
 * @property Text[] $texts
 */
class Task extends Commentable {
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{task}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, id_author, id_editor, id_pattern, created', 'required'),
			array('id, id_author, id_editor, id_pattern, id_text', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_author, id_editor, id_pattern, created, id_text', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @inherited
	 */
	public function CommentId() {
		return 1;
	}
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'keyphrases' => array(self::HAS_MANY, 'Keyphrase', 'id_task'),
			'texts' => array(self::HAS_MANY, 'Text', 'id_task'),
		) + parent::relations();
		//Строчка +parent::relations() делает эту модель комментируемой.
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'id_author' => 'Id Author',
			'id_editor' => 'Id Editor',
			'id_pattern' => 'Id Pattern',
			'created' => 'Created',
			'id_text' => 'Id Text',
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
		$criteria->compare('id_author',$this->id_author);
		$criteria->compare('id_editor',$this->id_editor);
		$criteria->compare('id_pattern',$this->id_pattern);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('id_text',$this->id_text);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Task the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @inherited
	 */
	public function checkCreateAccess() {
		return (parent::checkCreateAccess() && (Yii::app() -> user -> checkAccess('editor')));
	}
}
