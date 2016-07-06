<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property integer $id_type
 * @property string $username
 * @property string $password
 * @property string $name
 */
class User extends UModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_type, username, password', 'required'),
			array('id_type', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>256),
			array('password', 'length', 'max'=>60),
			array('name', 'length', 'max'=>512),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_type, username, password, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_type' => 'Id Type',
			'username' => 'Username',
			'password' => 'Password',
			'name' => 'Name',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('id_type',$this->id_type);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return string name og the view to be used to show the user
	 */
	public function view(){
		return 'author';
	}

	/**
	 * @param bool|string $arg defines what to search for
	 * @return User
	 */
	public function customFind($arg = false){
		if ($this -> scenario == 'cabinet') {
			$user = self::findByUsername($arg);
			if (is_a($user,'User')) {
				return $user;
			} else {
				return self::logged();
			}
		} else {
			return parent::customFind();
		}
	}
	/**
	 * We're overriding this method to fill findAll() and similar method result
	 * with proper models.
	 * @param array $attributes
	 * @return User
	 */
	protected function instantiate($attributes) {
		//Получаем все возможные типы действий
		$types = UserFactory::$types;
		//Ищем название класса
		$class = $types[$attributes['id_type']];
		if (!$class) {
			//Если не нашли, то ставим класс по умолчанию.
			$class = UserFactory::DefaultClass;
		}
		$class = ucfirst(strtolower($class));
		//Возвращаем новую модель.
		$model = new $class(null);
		return $model;
	}

	/**
	 * @return User - currently logged in user model
	 */
	public static function logged(){
		if (Yii::app() -> user -> isGuest) return false;
		return User::model()->findByPk(Yii::app()->user->getId());
	}
	public static function findByUsername ($username) {
		return self::model() -> findByAttributes(array('username' => $username));
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
