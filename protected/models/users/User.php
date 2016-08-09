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
 *
 * The followings are the available model relations:
 * @property Letter[] $newLetters
 */
class User extends UModel {
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
			'newLetters' => array(self::HAS_MANY, 'Letter', 'id_receiver',
				'condition' => 'opened = 0', 'group' => 'id_sender'),
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
	public function scopes() {
		return array(
			'author' => array('condition' => 'id_type = 3'),
		);
	}
	/**
	 * @return string name og the view to be used to show the user
	 */
	public function view(){
		return 'author';
	}

	/**
	 * @param bool|string $arg defines what to search for
	 * @return static
	 */
	public function customFind($arg = false){
		if (($this -> scenario == 'cabinet')&&($arg)) {
			$user = self::findByUsername($arg);
			if (is_a($user,'User')) {
				return $user;
			} else {
				return self::logged();
			}
		}
		if($this -> scenario == 'openDialog') {
			return self::findByUsername($arg);
		}
		return $this -> findByPk(Yii::app() -> user -> getId());
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

	/**
	 * @param string $string
	 */
	public function notify($string){
		$this -> send($string, true);
	}
	/**
	 * @param string|letter $string
	 * @param bool $system
	 * @return bool - whether the letter was sent
	 */
	public function send($string, $system = false) {
		if (!is_a($string,'Letter')) {
			$letter = new Letter();
			$letter->text = $string;
			$letter->id_receiver = $this->id;
			if (!$system) {
				$letter->id_sender = Yii::app()->user->getId();
			} else {
				$letter->id_sender = Letter::SYSTEM_SENDER;
			}
		} else {
			$letter = $string;
			$letter -> id_receiver = $this -> id;
			$letter -> isNewRecord = true;
		}
		return $letter -> save();
	}

	/**
	 * @param mixed[] $post
	 * @return bool
	 */
	public function sendJS($post){
		/*$rez = ['success' => false];
		if ($post['idSender'] != Yii::app() -> user -> getId()) {
			$rez['error'] = 'Отправитель и залогиненный пользователь не совпадают. Невозможно отправить.';
		} else {
			$receiver = $this -> findByPk($post['idReceiver']);
			if (!$receiver) {
				$rez['error'] = 'Получатель не найден.';
			} else {*/
		$receiver = User::findByPk($post['idReceiver']);
		$rez = $this -> dialogInitialDataCheck($post);
		if (!$rez['error']) {
			$letter = new Letter();
			$letter -> id_sender = $post['idSender'];
			$letter -> text = $post['text'];
			$rez['success'] = $receiver -> send($letter);
			if (!$rez['success']) {
				$rez['error'] = $letter -> getErrors();
			}
		}
		//}
		echo json_encode($rez);
	}
	public function dialogHistory($post){
		$rez = $this -> dialogInitialDataCheck($post);
		/*$rez = ['success' => false];
		if ($post['idSender'] != Yii::app() -> user -> getId()) {
			$rez['error'] = 'Отправитель и залогиненный пользователь не совпадают. Невозможно отправить.';
		} else {
			$receiver = $this -> findByPk($post['idReceiver']);
			if (!$receiver) {
				$rez['error'] = 'Получатель не найден.';
			} else {*/
		$idReceiver = $post['idReceiver'];
		if (!$rez['error']) {
			$sql = "SELECT * FROM `tbl_letters` WHERE (`id_sender`='$this->id' AND `id_receiver`='$idReceiver' OR `id_sender`='$idReceiver' AND `id_receiver`='$this->id' )";
			if ($post['date'] == 'first') {
				$sql .= ' ';
			} elseif(preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',$post['date'])) {
				$date = $post['date'];
				$sql .= " AND `sent` < '$date'";
			}
			if ((is_int($post['size']*1))&&($post['size']*1)) {
				$upper = $post['size'];
			} else {
				$upper = 5;
			}
			$sql .= " ORDER BY `sent` DESC LIMIT 0, $upper";
			$conn = MysqlConnect::getConnection();
			$query = mysqli_query($conn, $sql);
			$count = 0;
			if ($query) {
				$html = '';
				while ($array = mysqli_fetch_assoc($query)) {
					$count ++;
					$html = Yii::app()->controller->renderPartial('//cabinet/_letter', $array + [
									'class' => $array['id_sender'] == $this->id ? 'oneself' : 'opponent'
							], true) . $html;
					if ($array['opened'] == 0) {
						$rez['notRead'][] = $array['id'];
					}
					$rez['lastDate'] = $array['sent'];
				}
				$rez['html'] = $html;
				$rez['noMore'] = $count < $upper;
				$rez['success'] = true;
			} else {
				$rez['error'] = 'Запрос к базе данных завершился неудачей.';
			}
		}
		//}
		echo json_encode(array_filter($rez));
	}
	public function checkNewLetters($post) {
		$rez = $this -> dialogInitialDataCheck($post);
		/*$rez = ['success' => false];
		if ($post['idSender'] != Yii::app() -> user -> getId()) {
			$rez['error'] = 'Отправитель и залогиненный пользователь не совпадают. Невозможно отправить.';
		} else {
			$receiver = $this->findByPk($post['idReceiver']);
			if (!$receiver) {
				$rez['error'] = 'Получатель не найден.';
			} else {*/
		$idReceiver = $post['idReceiver'];
		if (!$rez['error']) {
			$sql = "SELECT * FROM `tbl_letters` WHERE (`id_sender`='$this->id' AND `id_receiver`='$idReceiver' OR `id_sender`='$idReceiver' AND `id_receiver`='$this->id' )";
			if ($post['date'] == 'first') {
				$sql .= ' ';
			} elseif(preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',$post['date'])) {
				$date = $post['date'];
				$sql .= " AND `sent` > '$date'";
			}
			$sql .= ' ORDER BY `sent` ASC';
			$query = mysqli_query(MysqlConnect::getConnection(),$sql);
			if ($query) {
				$html = '';
				while($array = mysqli_fetch_assoc($query)) {
					$html .= Yii::app() -> controller -> renderPartial('//cabinet/_letter',$array + [
									'class' => $array['id_sender'] == $this->id ? 'oneself' : 'opponent'
							], true);
					if ($array['opened'] == 0) {
						$rez['notRead'][] = $array['id'];
					}
					$rez ['firstDate'] = $array['sent'];
				}
				$rez['html'] = $html;
				if ($html) {
					$rez['success'] = true;
				}
			} else {
				$rez['error'] = 'Не удалось получить данные из базы данных.';
			}
		}
		echo json_encode($rez);
	}

	/**
	 * @param mixed[] $post
	 */
	public function read($post){
		$rez = ['success' => true];
		$crit = new CDbCriteria();
		$crit -> addInCondition('id',array_values($post['toRead']));
		$crit -> compare('id_receiver', Yii::app() -> user -> getId());
		$crit -> compare('opened', 0);
		$letters = Letter::model() -> findAll($crit);
		foreach ($letters as $letter) {
			$letter -> opened = 1;
			$rez['success'] &= $letter -> save();
		}
		echo json_encode($rez);
	}

	/**
	 * @param mixed[] $post
	 */
	public function openDialog($post){
		$loggedId = Yii::app() -> user -> getId();
		$rez['no'] = false;
		if ($this -> id != $loggedId) {
			$rez['html'] = Yii::app()->controller->renderPartial('//cabinet/dialog', ['model' => $this], true);
			$rez['idReceiver'] = $this->id;
			$rez['idSender'] = $loggedId;
			$rez['date'] = date('Y-m-d H:i:s');
		} else {
			$rez['no'] = true;
		}
		echo json_encode($rez);
	}
	/**
	 * @param mixed[] $post
	 * @return mixed[] $rez
	 */
	public function dialogInitialDataCheck($post) {
		$rez = ['success' => false];
		if ($post['idSender'] != Yii::app() -> user -> getId()) {
			$rez['error'] = 'Отправитель и залогиненный пользователь не совпадают. Невозможно отправить.';
		} else {
			$receiver = $this->findByPk($post['idReceiver']);
			if ((!$receiver)&&($post['idReceiver'] != 0)) {
				$rez['error'] = 'Получатель не найден.';
			}
		}
		return $rez;
	}

	/**
	 * @return string - link to dialog with this user
	 */
	public function show(){
		return CHtml::link($this -> name, "#",array('class' => 'dialogCreator', 'data-id' => $this -> username));
	}
}
