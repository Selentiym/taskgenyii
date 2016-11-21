<?php

/**
 * This is the model class for table "{{text}}".
 *
 * The followings are the available columns in table '{{text}}':
 * @property integer $id
 * @property integer $id_task
 * @property integer $length
 * @property string $text
 * @property bool $handedIn
 * @property bool $QHandedIn
 * @property bool $accepted
 * @property string $uid
 * @property string $updated
 * @property string $description
 * @property float $uniquePercent
 * @property string $shingles
 *
 * The followings are the available model relations:
 * @property Task $task
 */
class Text extends Commentable {
	const MIN_UNIQUE = 97;
	const MAX_NOSEA = 9;
	const MAX_NUCL_WORD = 4;
	const MAX_WORD = 4;

	const crossCheckNum = 3;
	private $temp = '';
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
				array('id_task', 'required'),
				array('id, id_task, length', 'numerical', 'integerOnly'=>true),
				array('text, description', 'safe'),
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
				'task' => array(self::BELONGS_TO, 'Task', 'id_task'),
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
	public function CustomFind($arg){
		switch($this -> scenario){
			case 'model':
				return $this;
				break;
			default:
				return $this -> findByPk($arg);
				break;
		}
	}

	/**
	 * Основная функция интерфейса для авторов.
	 * Считает ключевые слова, получает разные статистики из api, проверяет уникальность.
	 * Попутно сохраняет временные изменения в тексте вместе с некоторыми параметрами.
	 *
	 * Весь вывод идет в виде json-объекта.
	 * @var mixed[] $post
	 * @var bool $return - whether to print json or return resulting array
	 * @return mixed[]
	 */
	public function analyze($post, $return = false){
		$rez = array();
		$text = $post['text'];
		$this -> text = $text;
		//Сохраняем только текст
		if (!$post['not_save']) {
			$this -> handedIn = 0;
			$this -> QHandedIn = 0;
			$this -> save(false, array('text',"handedIn","QHandedIn"));
		}
		$text = new TextStringGlued($text);

		$i = -1;
		$keyPhrasesSorted = $this -> task -> getKeyphrasesSorted();
		foreach($keyPhrasesSorted as $phr){
			if ($phr -> direct > 0) {
				++$i;
				$temp = $text->lookForLiteral($phr -> phrase);
				$rez['phrs']['direct'][] = $temp;
				if ($temp < $phr -> direct) {
					$rez['failed']['direct'][] = array('phrase' => $phr -> phrase, 'need' => $phr -> direct, 'have' => $temp);
				}
			}
		}
		$i = -1;
		foreach($keyPhrasesSorted as $phr){
			if ($phr -> morph > 0) {
				//$i Является параметром, отделяющим разные фразы. Важно для морфовхождения,
				// так как слова могут быть раскиданы по предложению и фразы могут пересекаться.
				//В идеале потом выделять разным цветом разные фразы или что-то вроде.
				++$i;
				$temp = $text->lookForSentenced(new wordSet($phr->phrase, $i));
				$rez['phrs']['morph'][] = $temp;
				if ($temp < $phr -> morph) {
					$rez['failed']['morph'][] = array('phrase' => $phr -> phrase, 'need' => $phr -> morph, 'have' => $temp);
				}
			}
		}
		$rez['text'] = $text -> getToPrint();

		if ($return) {
			return $rez;
		}
		echo json_encode($rez, JSON_PRETTY_PRINT);
		return $rez;
	}
	/**
	 * Основная функция интерфейса для авторов.
	 * Считает ключевые слова, получает разные статистики из api, проверяет уникальность.
	 * Попутно сохраняет временные изменения в тексте вместе с некоторыми параметрами.
	 *
	 * Весь вывод идет в виде json-объекта.
	 * @var mixed[] $post
	 * @var bool $return - whether to print json or return resulting array
	 * @return array
	 */
	public function analyzeOld($post, $return = false){
		$rez = array();
		$text = $post['text'];
		$this -> text = $text;
		$text = new sentenceString(strip_tags($text));
		$i = -1;
		foreach($this -> task -> keyphrases as $phr){
			//++ $i;
			$rez['phrs'][] = $text -> lookFor(new arrayString($phr -> phrase));
		}


		if ($return) {
			return $rez;
		}
		echo json_encode($rez, JSON_PRETTY_PRINT);
		return $rez;
	}

	public function evalSickness() {
		$text = arrayString::removeRubbishFromString($this -> text);
		$words = preg_split('/\W+/ui',$text);
		$counted = [];
		$null = 0;
		foreach ($words as $word) {
			$stem = Stemmer::getInstance() -> stem_word_initial($word);
			if ($stem) {
				$counted[$stem]++;
			} else {
				$null++;
			}
		}
		$total = array_sum($counted);
		$numerator = pow(array_reduce($counted, function($prev, $el) {
			if ($el > 1) {
				$prev += pow($el,2);
			}
			return $prev;
		}, 0),0.5);
		$rez = [];
		if ($total < 1) {
			$total = 1;
		}
		$rez['sick'] = $numerator/$total;
		$rez['first_word_num'] = -1;
		foreach($counted as $stem => $num){
			if ($rez['first_word_num'] < $num) {
				$rez['first_word_num'] = $num;
				$rez['first_word'] = $stem;
			}
		}
		$rez['first_word_num'] /= $total;
		$rez['total'] = $total;
		$rez['words'] = $words;
		return $rez;
	}
	/**
	 * Возвращает параметры текста, которые нужно контролировать по ТЗ.
	 * Академическая тошнота, первая строка в семантическом ядре и словах.
	 * @param mixed[] $post
	 * @param bool $return
	 * @return mixed[]
	 */
	public function seoStat($post, $return = false) {
		$t = new Text();
		$t -> text = $post['text'];
		$temp = $t -> evalSickness();
		$rez['sick'] = round($temp['sick']*1000)/10;
		$rez['first_word_num'] = round($temp['first_word_num']*10000)/100;
		$rez['first_word'] = $temp['first_word'];
		if ($return) {
			return $rez;
		}
		//Выводим результат
		echo json_encode($rez, JSON_PRETTY_PRINT);
		return $rez;
	}
	/**
	 * @param mixed[] $post
	 * @param bool $return
	 * @return mixed[]
	 */
	public function fastUnique($post, $return = false){
		$str = arrayString::removeRubbishFromString($post['text']);
		$content = new ContentWatch($str);
		$content->sendRequest();
		$rez = $content->summary();
		$toSave = ['text'];
		$this -> text = $post['text'];
		if ($rez['percent']) {
			$this -> uniquePercent = $rez['percent'];
			$toSave[] = 'uniquePercent';
			$this -> uid = new CDbExpression('NULL');
			$toSave[] = 'uid';
		}
		$this -> save($toSave);
		if ($return) {
			return $rez;
		}
		echo json_encode($rez);
		return $rez;
	}
	/**
	 * @param mixed[] $post
	 * @param bool $return
	 * @return mixed[]
	 */
	public function addUnique($post = array(), $return = false){
		if (($post['text'])) {
			$this -> text = $post['text'];
		}
		$rez = TextRuApiHelper::addPost($this -> text, Yii::app() -> createAbsoluteUrl('text/uniqueResult',array('arg' => $this -> id)));
		if ($rez['text_uid']) {
			$this -> uid = $rez['text_uid'];
			$this -> uniquePercent = new CDbExpression('NULL');
			if (!$this -> save(true, array('uid','uniquePercent','text'))){
				$err = $this -> getErrors();
			}
		}
		if ($return) {
			return $rez;
		}
		echo json_encode($rez, JSON_PRETTY_PRINT);
		return $rez;
	}

	/**
	 * @param bool $print
	 * @return float
	 */
	public function giveUnique($print = false) {
		//Если нет процента, пытаемся его получить.
		if (!$this -> uniquePercent) {
			$answer = [];
			if ($this->uid) {
				$answer = TextRuApiHelper::getResultPost($this->uid);
			}
			if ($answer['text_unique']) {
				$this->uniquePercent = $answer['text_unique'];
				$this->save(false, array('uniquePercent', 'uid'));
			}
		}
		if ($print) {
			echo json_encode(array(
				'uid' => $this -> uid,
				'unique' => $this -> uniquePercent
			));
		}
		return $this -> uniquePercent;
	}
	public function uniqueResult($post){
		$this -> uid = $post['uid'];
		$this -> uniquePercent = $post['text_unique'];
		$this -> save();
		echo 'ok';
	}

	/**
	 * Функция сканирует БД, достает все принятые работы. Проверяет их быстрым образом, выбирает
	 * несколько "самых похожих", проверяет их более подробно и возвращает одну с максимальным
	 * числом совпадений.
	 * @param bool $return
	 * @return array
	 * @throws DatabaseException
	 */
	public function crossUnique($return = false){
		$conn = MysqlConnect::getConnection();
		$q = mysqli_query($conn,"SELECT `id`, `shingles` FROM `tbl_text` WHERE `accepted`='1' and `shingles` IS NOT NULL");
		$rez = [];
		if ($q) {
			$main = $this -> fastShingles();
			while ($temp = $q->fetch_array(MYSQLI_NUM)) {
				$rez [$temp[0]] = $main->compare(new \Shingles\Fast($temp[1], true));
			}
			//Ключи $rez - айдишники, а значения - величины перекрывания
			asort($rez);
			$toCheck = array_keys(array_slice($rez, -self::crossCheckNum, null ,true));
			$texts = Text::model()->findAllByPk($toCheck);
			$cur = new \Shingles\Full($this->text);
			$matches = [];
			$t = false;
			$max = -1;
			foreach ($texts as $text) {
				$shingle = new \Shingles\Full($text->text);
				$compare = $cur->compare($shingle);
				if ($compare > $max) {
					$max = $compare;
					$matches = $cur->dump;
					$t = $text;
					$sh = $shingle;
				}
			}
			$bold = (bool) $matches[0];
			//Собираем обратно текст
			$shinglesToShow = $sh -> giveShingles();
			$words = [];
			foreach ($shinglesToShow as $i => $s ) {
				$curWs = explode(' ',$s[1]);
				foreach ($curWs as $j => $w) {
					$words [$i + $j] = [$w, (bool)$matches[$i]];
				}
			}
			$rezT = '';
			foreach ($words as $word) {
				if ($word[1]) {
					$rezT .= ' <b>'. $word[0].'</b>';
				} else {
					$rezT .=' '. $word[0];
				}
			}
		}
		/**
		 * @type Text $t
		 */
		if ($t) {
			$rez = [
					'text' => $rezT,
					'matches' => array_values($matches),
					'percent' => $max
			];
		} else {
			$rez = [
				'percent' => -1
			];
		}
		if ($return) {
			return $rez;
		}
		echo json_encode($rez);
		return $rez;
	}
	/**
	 * @return \Shingles\Fast
	 */
	public function fastShingles() {
		if ($this -> shingles) {
			return new \Shingles\Fast($this -> shingles, true);
		} else {
			return new \Shingles\Fast($this -> text);
		}
	}
	protected function beforeSave() {
		$nullStatus = true;
		switch ($this -> scenario) {
			case 'noBeforeSave' :
				return true;
				break;
			case 'handIn':
				$report = $this -> checkMath();
				if ($report === true) {
					$this -> handedIn = true;
					$task = $this -> task;
					if ($this -> task -> editor) {
						$this->task->editor->notify('Текст по заданию ' . $task->show() . ' сдан.');
					}
					Yii::app() -> user -> setState('textHandIn',$report);
				} else {
					Yii::app() -> user -> setState('textHandIn',$report);
					$flashes = Yii::app() -> user -> getFlashes(false);
					$this -> addError('text',$report);
					return false;
				}
				$nullStatus = false;
				break;
			case 'handInWithMistakes':
				$db = $this -> DBModel();
				if (arrayString::leaveOnlyLetters($this -> text) != arrayString::leaveOnlyLetters($db -> text)) {
					$this -> setScenario('checkMath');
					$unique = $this -> fastUnique(['text' => $this -> text] , true);
					$this -> uniquePercent = $unique['percent'];
					$this -> setScenario('handInWithMistakes');
				}
				if (!$this -> uniquePercent) {
					Yii::app() -> user -> setState('textHandIn','Проверка на уникальность не проведена или завершена с ошибкой.');
					return false;
				} else {
					if ($this -> uniquePercent < 80) {
						Yii::app() -> user -> setState('textHandIn','Уникальность ниже 80%, что неприемлемо в любом случае.');
						return false;
					}
				}
				$this -> QHandedIn = 1;
				$nullStatus = false;
				$task = $this -> task;
				if ($this->task->editor) {
					$this->task->editor->notify('Поступила просьба рассмотреть текст по заданию ' . $task->show() . '.');
				}
				break;
			case 'accept':
				//if (Yii::app() -> user -> checkAccess('administrateTask',['task' => $this -> task])) {
				if (Yii::app() -> user -> checkAccess('editor')) {
					$this -> accepted = 1;
					$this -> shingles = $this -> fastShingles() -> archive();
					$task = $this -> task;
					$task -> id_text = $this -> id;
					$task -> save();
					if ($task->author) {
						$task->author->notify('Текст по заданию ' . $task->show() . ' принят!');
					}
				} else {
					return false;
				}
				break;
			case 'decline':
				//if (Yii::app() -> user -> checkAccess('administrateTask',['task' => $this -> task])) {
				if (Yii::app() -> user -> checkAccess('editor')) {
					$text = $this -> task -> createText($this);
					$this -> accepted = 0;
					if (!$text -> save()) {
						$ar = $text -> getErrors();
					}
					if ($this -> task -> author) {
						$this->task->author->notify('Текст по заданию ' . $this->task->show() . ' отклонен. Более подробную информацию ищите в комментариях.');
					}
				} else {
					return false;
				}
				break;
			case 'delay':
				break;
			case 'checkMath':
				return false;
				break;
		}
		/**
		 * Сбрасываем уникальность, если меняется текст, а также сбрасываем статусы текста.
		 */
		if (!$this -> isNewRecord) {
			$db = $this->DBModel();
			if (arrayString::removeRubbishFromString($this->text) != arrayString::removeRubbishFromString($db->text)) {
				//Если процент уникальности меняется, значит сохранение идет из функции проверки кникальности.
				//Само собой текст при этом тоже поменяется. Плохо будет ставить уникальность в ноль тогда.
				if ($this -> uniquePercent == $this -> DBModel() -> uniquePercent) {
					$this->uniquePercent = new CDbExpression('NULL');
					$this->uid = new CDbExpression('NULL');
				}
				if ($nullStatus) {
					$this->handedIn = 0;
					$this->QHandedIn = 0;
				}
			}
		}
		/**
		 * Считаем длину
		 */
		$this -> length = $this -> countLength(false);
		//Обновляем время обновления
		if (!is_a($this -> updated, 'CDbExpression')) {
			$this->updated = new CDbExpression("CURRENT_TIMESTAMP");
		}
		return parent::beforeSave();
	}

	/**
	 * @return bool|string Текст ошибки или true
	 */
	public function checkMath() {
		$oldScenario = $this -> getScenario();
		$this -> setScenario('mathCheck');
		$log = function($str) {
			$this -> temp .= $str.'<br/>';
		};
		/**
		 * Самая сложная проверка - на уникальность
		 */
		$str1 = arrayString::leaveOnlyLetters($this -> text);
		$str2 = arrayString::leaveOnlyLetters($this -> DBModel() -> text);
		$len1 = strlen($str1);
		$len2 = strlen($str2);
		if ($str1 != $str2) {
			$unique = $this -> fastUnique(['text' => $this -> text], true);
			if ($unique['success']) {
				$this->uniquePercent = $unique['percent'];
			}
		}
		if ($this -> uniquePercent) {
			if ($this -> uniquePercent < self::MIN_UNIQUE) {
				$log('Уникальность ниже '.self::MIN_UNIQUE.'%, пожалуйста, исправьте.');
			}
		} else {
			$log('Проверка на уникальность не проводилась или выполнена с ошибкой.');
		}
		/**
		 * Проверка на длину текста
		 */
		$this -> countLength(false);
		if ($this -> task -> min_length > 0) {
			if ($this -> length < $this -> task -> min_length) {
				$log('Длина текста недостаточна.');
			}
		}
		if ($this -> task -> max_length > 0) {
			if ($this -> length > $this -> task -> max_length) {
				$log('Текст слишком длинный.');
			}
		}
		//Если текст никак не изменился с момента последней проверки на уникальность
		/*if ($str1 == $str2) {
			//Тогда смотрим уникальность
			if ($this -> uniquePercent < self::MIN_UNIQUE) {
				$log('Уникальность ниже '.self::MIN_UNIQUE.'%, пожалуйста, исправьте.');
			}
		} else {

			$log('Проверка на уникальность не актуальна, пожалуйста, проведите проверку перед очередной попыткой сдать статью.');
		}*/

		/**
		 * Проверка на seo параметры
		 */
		$seo = $this -> seoStat(['text' => $this -> text], true);
		if ($seo['first_nucl_num'] > self::MAX_NUCL_WORD) {
			$log('Первый показатель в семантическом ядре превышает '.self::MAX_NUCL_WORD.'.');
		}
		if ($seo['first_word_num'] > self::MAX_WORD) {
			$log('Первый показатель в словах превышает '.self::MAX_WORD.".");
		}
		if ($seo['sick'] > self::MAX_NOSEA) {
			$log('Тошнотность превышает '.self::MAX_NOSEA.".");
		}
		$keys = $this -> analyze(['text' => $this -> text], true);
		if (!empty($keys['failed']['direct'])) {
			foreach ($keys['failed']['direct'] as $phr) {
				$log ('Фраза "' . $phr["phrase"] . '" имеет недостаточное количество прямых вхождений: ' . $phr['have'] . '/' . $phr['need'] . '.');
			}
		}
		if (!empty($keys['failed']['morph'])) {
			foreach ($keys['failed']['morph'] as $phr) {
				$log ('Фраза "'.$phr["phrase"].'" имеет недостаточное количество морфологических вхождений: '.$phr['have'].'/'.$phr['need'].'.');
			}
		}
		$this -> setScenario($oldScenario);
		//$string = $log('');
		if (!$this -> temp) {
			return true;
		} else {
			return $this -> temp;
		}
	}
	public function countLength($save = true, $text = null){
		if ($text === null) {
			$text = $this -> text;
		} else {
			$this -> text = $text;
		}
		$length = mb_strlen(arrayString::leaveOnlyLetters($text),'utf-8') + mb_strlen(arrayString::leaveOnlyLetters($this -> description),'utf-8');
		if ($save) {
			$old = $this -> getScenario();
			$this -> length = $length;
			$this -> setScenario('noBeforeSave');
			$this -> save(['length']);
			$this -> setScenario($old);
		}
		return $length;
	}
	public function countTextJS($post){
		$this -> text = $post['text'];
		$this -> description = $post['description'];
		echo json_encode(['length' => $this -> countLength(true)]);
	}
}
