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
 * @property integer $min_length
 * @property integer $max_length

 *
 * The followings are the available model relations:
 * @property Keyphrase[] $keyphrases
 * @property SearchPhrase[] $searchphrases
 * @property int $searchphrasesCount
 * @property Keyword[] $keywords
 * @property Text[] $texts
 * @property Text $currentText
 * @property Text $currentlyWrittenText
 * @property Text $rezult
 * @property Task $parent
 * @property Task[] $children
 * @property Pattern $pattern
 * @property Author $author
 * @property Editor $editor
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
	/**
	 * @var bool $notifyAuthorFlag
	 */
	public $notifyAuthorFlag = false;
	/**
	 * @var int $toTextRedirect
	 */
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
			array('id, id_author, id_editor, id_pattern, created, id_text, name, min_length, max_length', 'safe', 'on'=>'search'),
			array('id_author, id_pattern, phrases, name, min_length, max_length', 'safe', 'on'=>'create'),
			array('id_author, id_pattern, phrases, name, min_length, max_length', 'safe', 'on'=>'generate'),
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
			'searchphrasesCount' => array(self::STAT, 'SearchPhrase', 'id_task'),
			'notAcceptedTimes' => array(self::STAT, 'Text', 'id_task', 'condition' => 'accepted=0'),
			'notAcceptedTexts' => array(self::HAS_MANY, 'Text', 'id_task', 'condition' => 'accepted=0'),
			'keywords' => array(self::HAS_MANY, 'Keyword', 'id_task'),
			'texts' => array(self::HAS_MANY, 'Text', 'id_task', 'order' => 'updated DESC'),
			//'currentText' => array(self::HAS_ONE, 'Text', 'id_task', 'condition' => 'handedIn = 1', 'order' => 'updated DESC'),
			'currentText' => array(self::HAS_ONE, 'Text', 'id_task', 'order' => 'updated DESC'),
			'currentlyWrittenText' => array(self::HAS_ONE, 'Text', 'id_task', 'condition' => 'handedIn = 0', 'order' => 'updated DESC'),
			'rezult' => array(self::BELONGS_TO, 'Text', 'id_text'),
			'parent' => array(self::BELONGS_TO, 'Task', 'id_parent'),
			'children' => array(self::HAS_MANY, 'Task', 'id_parent'),
			'pattern' => array(self::BELONGS_TO, 'Pattern', 'id_pattern'),
			'author' => array(self::BELONGS_TO, 'Author', 'id_author'),
			'editor' => array(self::BELONGS_TO, 'Editor', 'id_editor'),
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
	public function notifyAuthorAboutAssign(){
		if ($this -> notifyAuthorFlag) {
			Author::model() -> findByPk($this -> id_author) -> notify('Вам было присвоено задание '.$this -> show().'.');
		}
	}
	protected function beforeSave() {
		//Хочу оповестить автора о присвоении ему задания.
		if ($this -> id_author) {
			$this -> notifyAuthorFlag = $this -> DBModel() -> id_author != $this -> id_author;
		}
		if ($this -> getScenario() == 'move') {
			if ($toMove = Task::model() -> findByPk($_GET['where'])) {
				$this->id_parent = $toMove -> id;
			}
		}
		return parent::beforeSave();
	}

	protected function afterSave(){
		//Вызываем после получения id.
		$this -> notifyAuthorAboutAssign();

		if (!empty($this -> phrases['text'])) {
			foreach ($this->phrases['text'] as $key => $phr) {
				if (!($this -> phrases['changed'][$key])) {
					continue;
				}
				$kp = new Keyphrase();
				$kp->id_task = $this->id;
				$kp->phrase = $phr;
				$kp->direct = $this->phrases ['strict'][$key];
				$kp->morph = $this->phrases ['morph'][$key];
				$kp->freq = $this->phrases ['freq'][$key];
				$kp->tag = $this->phrases ['tag'][$key];
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
					 echo '';
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
			//Стараемся не сохранять стопы и прочий мусор
			if (trim(arrayString::removeRubbishFromString($key -> word))) {
				$this->addKey($key);
			}
		},array_map('trim',preg_split("/\r\n/",$this -> keystring)));
		return parent::afterSave();
	}
	public function customFind($arg = false) {
		if ($arg) {
			if (($this->scenario == 'view')||($this -> scenario == 'make')) {
				return $this -> findByPk($arg);
			}
			if ($this -> getScenario() == 'move') {
				//Чтобы прошло сохранение.
				$_POST['Task'] = [];
			}
		}
		if ($this -> getScenario() == 'deleteGroup') {
			return self::model();
		}
		return $this -> findByPk($arg);
	}

	/**
	 * @param Text $t - text to be based on. If new record then nothing but a task id will be set
	 * @return Text
	 */
	public function createText(Text $t){
		if ($t -> isNewRecord) {
			$text = $t;
		} else {
			$text = new Text();
			$text -> text = $t -> text;
			$text -> uniquePercent = $t -> uniquePercent;
			$text -> uid = $t -> uid;
		}
		$text -> id_task = $this -> id;
		$text -> updated = new CDbExpression('CURRENT_TIMESTAMP');
		return $text;
	}
	/**
	 * Creates a new text if necessary
	 * @return bool|Text the created text
	 */
	public function prepareTextModel(){
		$id = false;
		//Если задание выполнено, то точно создавать ничего не нужно.
		if ($this -> id_text) {
			return false;
		}
		$current = $this -> currentText;
		if (!$current) {
			$text = $this -> createText(new Text());
			$text -> save();
			return $text;
		}
		if ($current -> accepted === null) {
			return false;
		} else {
			$text = $this -> createText($current);
			$text -> save();
			return $text;
		}


		/*if (!$this -> currentlyWrittenText) {
			$text = new Text;
			$text->id_task = $this->id;
			if ($old = $this -> currentText) {
				$text -> text = $old -> text;
				$text -> uniquePercent = $old -> uniquePercent;
				$text -> uid = $old -> uid;
			}
			if ($text->save()) {
				$id = $text->id;
			}
		} else {
			$text = $this -> currentlyWrittenText;
			$id = $this -> currentlyWrittenText -> id;
		}
		$this->toTextRedirect = Yii::app()->createUrl('task/view', array('arg' => $this -> id));
		return $text;*/
	}

	/**
	 * @param $url - initial url to be redirected to
	 * @return string|array - redirect path
	 */
	public function redirectAfterTextCreate($url){
		return Yii::app()->createUrl('task/view', array('arg' => $this -> id));
		/*if ($this -> toTextRedirect) {
			return $this->toTextRedirect;
		} else {
			return $url;
		}*/
	}

	public function renewVocabularyWithSearchPhrase(SearchPhrase $phrase){
		$words = explode(' ', $phrase -> phrase);
		array_map(function($word){
			$word = arrayString::removeRubbishFromString($word);
			if (!$word) {
				return false;
			}
			$key = new Keyword();
			$key -> word = $word;
			$key -> num = 1;
			return $this -> addKey($key);
		},$words);
	}
	public function renewVocabularyRemoveSearchPhrase(SearchPhrase $phrase){
		$words = explode(' ', $phrase -> phrase);
		array_map(function($word){
			$word = arrayString::removeRubbishFromString($word);
			if (!$word) {
				return false;
			}
			$key = new Keyword();
			$key -> word = $word;
			$key -> num = 1;
			return $this -> removeKey($key);
		},$words);
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
		//Если этого слова пока нет, то просто добавляем его
		$key -> id_task = $this -> id;
		return $key -> save();
	}
	/**
	 * @param Keyword $key
	 * добавляет ключ, если нет уже такого, проводит поиск
	 * посредством приведения к стандартной форме.
	 * @return bool - удалили ли слово
	 */
	public function removeKey(Keyword $key){
		foreach ($this -> keywords as $has) {
			$flag = $has -> giveLemma() == $key -> giveLemma();
			if ($flag) {
				$has -> num -= $key -> num;
				if ($has -> num <= 0) {
					return $has -> delete();
				}
				return $has -> save();
				//Значит мы нашли нужную строку
				//preg_match('/(^|, )'.$.'($|, )/', $has -> word);
				//попытка дописывать формы, но зачем?
			}
		}
		return false;
	}

	/**
	 * @return string - a link to the task
	 */
	public function show(){
		return CHtml::link($this -> name, Yii::app() -> createUrl('task/view',['arg' => $this -> id]));
	}

	/**
	 *
	 */
	public function deleteGroup($post){
		$arr = [];
		if (count($post['ids']) > 0) {
			$models = $this->findAllByPk($post['ids']);;
			$rez = '';
			$reload = false;
			foreach ($models as $model) {
				if ($post["forced"] == 1) {
					$model -> setScenario("ignoreTexts");
				}
				if (!$model -> delete()) {
					$rez .= 'При удалении задания '.$model -> name.' возникла ошибка.';
					$hasTexts = $model -> getError('id');
					$rez .= $hasTexts;
					$hasChildren = $model -> getError('id_parent');
					$rez .= $hasChildren;
					$rez .= PHP_EOL;
					if (($hasTexts)&&(!$hasChildren)) {
						$temp = [
							'id' => $model -> id,
							'mes' => 'Задание '.$model -> name.' имеет текст(ы). Точно удалить?'
						];
						$forcedDel[] = $temp;
					}
				} else {
					$reload = true;
				}
			}
			if (!$rez) {
				$rez = 'Удаление успешно завершено!';
			}
		} else {
			$rez = false;
		}
		$arr['commonMess'] = $rez;
		$arr['forcedDel'] = $forcedDel;
		$arr['reload'] = $reload;
		echo json_encode($arr);
	}

	/**
	 * Удаляем все поисковые фразы и все клчевые слова задания.
	 * Ключевые фразы, составленные редактором, не затрагиваются!
	 */
	public function deleteKeys(){
		SearchPhrase::model() -> deleteAllByAttributes(['id_task' => $this -> id]);
		Keyword::model() -> deleteAllByAttributes(['id_task' => $this -> id]);
		Keyphrase::model() -> deleteAllByAttributes(['id_task' => $this -> id]);
	}
	public function beforeDelete() {

		if (count($this -> children) > 0) {
			$this -> addError('id_parent','У задания есть потомки.');
			return false;
		}
		if ($this -> getScenario() != "ignoreTexts") {
			if (count($this->texts) > 1) {
				$this->addError('id', 'К заданию написаны несколько текстов.');
				return false;
			}
			if ($this->currentText->length > 1000) {
				$this->addError('id', 'Текущий текст длинее 1000 символов.');
				return false;
			}
		}
		foreach ($this -> texts as $text) {
			$text -> delete();
		}
		return parent::beforeDelete();
	}
	public function createDescendantFast(){
		$task = new Task();
		$task -> attributes = $this -> attributes;
		$task -> created = null;
		$task -> name = $_POST["name"];
		$task -> id_parent = $this -> id;
		$task -> id = null;
		if (!$task -> save()) {
			$rez['success'] = false;
		} else {
			$rez['success'] = true;
			$rez['id'] = $task -> id;
		}
		$rez["dump"] = $task -> dumpForProject();
		echo json_encode($rez);
	}
	public function renameJS(){
		$this -> name = $_POST["entered"];
		$this -> save();
		$rez["toShow"] = $this -> findByPk($this -> id) -> name;
		echo json_encode($rez);
	}
	public function dumpForProject(){
		$text = $this -> currentText;
		/**
		 * @type Text $text
		 */
		if ($this -> author) {
			$name = $this -> author -> name;
		} else {
			$name = '';
		}
		$arr = array('id' => $this -> id, 'name' => $this -> name, 'extra' => [
				'handedIn' => $text -> handedIn,
				'QHandedIn' => $text -> QHandedIn,
				'accepted' => $text -> accepted,
				'authorName' => $name,
				'authorHtml' => $this -> id_author,
				'hasKeys' => $this -> searchphrasesCount,
				'hasChildren' => (Task::model() -> countByAttributes(['id_parent' => $this -> id]) > 0),
				'keysGenerated' => (Keyphrase::model() -> countByAttributes(['id_task' => $this -> id]) > 0),
				'notEmpty' => (int)(mb_strlen(arrayString::leaveOnlyLetters($text -> text),"UTF-8") > 10)
		]);
		return $arr;
	}
	public function getKeyphrasesSorted(){
		$keyPhrasesSorted = $this -> keyphrases;
		usort($keyPhrasesSorted, function($k1, $k2){
			/**
			 * @type KeyPhrase $k1
			 */
			if ($k1 -> getMeaningfulWordsCount() > $k2 -> getMeaningfulWordsCount()) {
				return -1;
			} elseif ($k1 -> getMeaningfulWordsCount() < $k2 -> getMeaningfulWordsCount()) {
				return 1;
			}
			return 0;
		});

		return $keyPhrasesSorted;
	}
	public function assignAuthor(){
		$author = Author::model() -> findByPk($_POST["author"]);
		if ($author) {
			$this -> id_author = $author -> id;
			$this -> save();
		}
	}
}
