<?php

/**
 * This is the model class for table "{{task}}".
 *
 * The followings are the available columns in table '{{task}}':
 * @property integer $id
 * @property string $name
 * @property integer $id_author
 * @property integer $id_editor
 * @property integer $id_pattern
 * @property integer $id_parent
 * @property string $created
 * @property integer $id_text
 *
 * The followings are the available model relations:
 * @property Keyphrase[] $keyphrases
 * @property SearchPhrase[] $searchphrases
 * @property Keyword[] $keywords
 * @property Text[] $texts
 * @property Text $currentText
 * @property Task $parent
 * @property Task[] $children
 * @property Pattern $pattern
 * @property Author $author
 */
class Task extends Commentable {
	/**
	 * @var array[] $phrases массив ключевых фраз при создании/изменении модели
	 */
	public $phrases = array();
	/**
	 * @var string $input_search - данные из textarea при добавлении ключевых фраз
	 */
	public $input_search;
	/**
	 * @var string $input_search - данные из textarea по кластеру
	 */
	public $keystring;
	public $toTextRedirect;
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
			array('id_editor, id_pattern, name', 'required'),
			array('id, id_author, id_editor, id_pattern, id_text', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_author, id_editor, id_pattern, created, id_text, name', 'safe', 'on'=>'search'),
			array('id_author, id_pattern, phrases, name', 'safe', 'on'=>'create'),
			array('id_author, id_pattern, phrases, name', 'safe', 'on'=>'generate'),
			array('input_search, keystring', 'safe', 'on'=>'addKeywords'),
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
			'searchphrases' => array(self::HAS_MANY, 'SearchPhrase', 'id_task'),
			'keywords' => array(self::HAS_MANY, 'Keyword', 'id_task'),
			'texts' => array(self::HAS_MANY, 'Text', 'id_task'),
			'currentText' => array(self::HAS_ONE, 'Text', 'id_task', 'condition' => 'handedIn = 1'),
			//'currentlyWrittenText' => array(self::HAS_ONE, 'Text', 'id_task', 'condition' => 'handedIn = 0'),
			'parent' => array(self::BELONGS_TO, 'Task', 'id_parent'),
			'children' => array(self::HAS_MANY, 'Task', 'id_parent'),
			'pattern' => array(self::BELONGS_TO, 'Pattern', 'id_pattern'),
			'author' => array(self::BELONGS_TO, 'Author', 'id_author'),
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
	/**
	 * @inherit
	 */
	public function readData($data){
		//Создает задание тот, кто сейчас залогинен
		$this -> id_editor = Yii::app() -> user -> getId();
		//В дальнейшем будет древовидная структура
		$this -> id_parent = $data['parentId'];
    }
	public function scopes(){
		return array(
			'root' => array('condition' => 'id_parent = 0'),
			'uncategorized' => array('condition' => 'id_parent IS NULL'),
		);
	}
	protected function afterSave(){
		if (!empty($this -> phrases['text'])) {
			//Пока что только при создании, при обновлении такого нет.
			foreach ($this->phrases['text'] as $key => $phr) {
				if (!($this -> phrases['changed'][$key])) {
					continue;
				}
				$kp = new Keyphrase();
				$kp->id_task = $this->id;
				$kp->phrase = $phr;
				$kp->direct = $this->phrases ['strict'][$key];
				$kp->morph = $this->phrases ['morph'][$key];
				if (!$kp->save()) {
					$temp = $kp->getErrors();
				}
			}
		}
		//Удаляем ненужные записи
		if (count($this -> phrases['toDel']) > 0) {
			$crit = new CDbCriteria();
			$crit->addInCondition('id', $this->phrases['toDel']);
			Keyphrase::model() -> deleteAll($crit);
		}
		/**
		 * Добавление поисковых фраз из textarea
		 */
		$ar = preg_split("/\r\n/", $this -> input_search);
		array_map(function($el){
			$temp = array_map('trim',preg_split("/\t/",trim($el)));
			if ($temp[0]) {
				$phr = new SearchPhrase('create');
				$phr -> attributes = array(
						'phrase' => $temp[0],
						'baseFreq' => $temp[3],
						'phraseFreq' => $temp[4],
						'directFreq' => $temp[5],
						'id_task' => $this -> id
				);
				 if ($phr -> save()) {

				 } else {
					 $err = $phr -> getErrors();
				 }
			}
			return false;
		},$ar);
		/**
		 * Добавление "библиотеки" ключевых слов, тоже из textarea
		 */
		//Получили список кластеризованных слов.
		array_map(function($el){
			$temp = array_map('trim',preg_split("/\t/",trim($el)));
			$key = new Keyword();
			$key -> num = $temp[1];
			$key -> word = $temp[0];
			$this -> addKey($key);
		},array_map('trim',preg_split("/\r\n/",$this -> keystring)));
		return parent::afterSave();
	}
	public function customFind($arg = false) {
		if ($arg) {
			if (($this->scenario == 'view')||($this -> scenario == 'make')) {
				return $this -> findByPk($arg);
			}
		}
		return $this -> findByPk($arg);
	}

	/**
	 * Creates a text and saves its id to redirect afterwards
	 */
	public function lastText(){
		$id = false;
		if (!$this -> currentText) {
			$text = new Text;
			$text->id_task = $this->id;
			if ($text->save()) {
				$id = $text->id;
			}
		} else {
			$text = $this -> currentText;
			$id = $this -> currentText -> id;
		}
		if ($id) {
			$this->toTextRedirect = Yii::app()->createUrl('text/write', array('arg' => $id));
		}
		return $text;
	}

	/**
	 * @param $url - initial url to be redirected to
	 * @return string|array - redirect path
	 */
	public function redirectAfterTextCreate($url){
		if ($this -> toTextRedirect) {
			return $this->toTextRedirect;
		} else {
			return $url;
		}
	}

	/**
	 * @param Keyword $key
	 * добавляет ключ, если нет уже такого, проводит поиск
	 * посредством приведения к стандартной форме.
	 * @return bool - получилось ли сохранить ключевик
	 */
	public function addKey(Keyword $key){
		foreach ($this -> keywords as $has) {
			if ($has -> giveLemma() == $key -> giveLemma()) {
				$has -> num += $key -> num;
				return $has -> save();
				//Значит мы нашли нужную строку
				//preg_match('/(^|, )'.$.'($|, )/', $has -> word);
				//попытка дописывать формы, но зачем?
			}
		}
		//Если этого слова пока нет, то ничего страшного, просто добавляем его
		$key -> id_task = $this -> id;
		return $key -> save();
	}
}
