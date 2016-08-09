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
		if ($this -> scenario == 'write') {
			return $this -> findByPk($arg);
		}
		return $this -> findByPk($arg);
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
			$this -> save(false, array('text'));
		}
		$text = new textString(strip_tags($text));

		$i = -1;
		foreach($this -> task -> keyphrases as $phr){
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
		foreach($this -> task -> keyphrases as $phr){
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

	/**
	 * Возвращает параметры текста, которые нужно контролировать по ТЗ.
	 * Академическая тошнота, первая строка в семантическом ядре и словах.
	 * @param mixed[] $post
	 * @param bool $return
	 */
	public function seoStat($post, $return = false) {
		$out = '';
		 //очищаем от непонятных спецсимволов и тегов
		$post['text'] = preg_replace('/\&[a-zA-Z]+\;/u', ' ', strip_tags($post['text']));
		//Обращаемся на advego/text/seo/ с просьбой проанализировать текст
		if ($curl = curl_init()) {
			curl_setopt($curl, CURLOPT_URL, 'http://advego.ru/text/seo/');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			$query = http_build_query(array(
					'job_text' => $post['text']
			));
			curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
			$out = curl_exec($curl);
			curl_close($curl);
		}
		//Удаляем кучу лишнего и делаем то, что осталось, валидным
		$out = substr($out, strpos($out, 'text_check_results'));
		$out = substr($out, strpos($out, '>') + 1);
		$out = substr($out, 0, strpos($out, 'job_desc'));
		$out = substr($out, 0, strrpos($out, '<'));
		$out = substr($out, 0, strrpos($out, '>') + 1);

		$out = '<xml>' . $out;
		$out .= '</xml>';

		//Создали xml структуру, которую теперь можно рапарсить
		$xml = new SimpleXMLElement($out);

		//Тошнотность
		$temp = $xml->table->tr[10]->td[1];
		$rez['sick'] = preg_replace('/[^\d\.\,]/', '', $temp);
		if ($xml->div[0]->table->tr[1]) {
			//Первый показатель в семнатическом ядре
			$rez['first_nucl_num'] = preg_replace('/[^\d\.\,]/', '', $xml->div[0]->table->tr[1]->td[2]);
			$rez['first_nucl'] = "<table>" . $xml->div[0]->table->tr[1]->asXML() . "</table>";
		}
		if ($xml->div[1]->table->tr[1]) {
			//Первый показатель в словах
			$rez['first_word_num'] = preg_replace('/[^\d\.\,]/', '', $xml->div[1]->table->tr[1]->td[2]);
			$rez['first_word'] = "<table>" . $xml->div[1]->table->tr[1]->asXML() . "</table>";
		}
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
	public function addUnique($post = array(), $return = false){
		if (($post['text'])) {
			$this -> text = $post['text'];
		}
		$rez = TextRuApiHelper::addPost($this -> text, Yii::app() -> createAbsoluteUrl('text/uniqueResult',array('arg' => $this -> id)));
		if ($rez['text_uid']) {
			$this -> uid = $rez['text_uid'];
			$this -> uniquePercent = new CDbExpression('NULL');
			$this -> save(true, array('uid','unique','text'));
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


			$toCheck = array_slice(array_flip($rez), 0, self::crossCheckNum);
			$texts = Text::model()->findAllByPk($toCheck);
			$cur = new \Shingles\Full($this->text);
			$matches = [];
			$t = false;
			$max = -1;
			foreach ($texts as $text) {
				$compare = $cur->compare(new \Shingles\Full($text->text));
				if ($compare > $max) {
					$max = $compare;
					$matches = $cur->dump;
					$t = $text;
				}
			}
		}
		/**
		 * @type Text $t
		 */
		if ($t) {
			$rez = [
					'text' => $t->text,
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
		switch ($this -> scenario) {
			case 'handIn':
				$report = $this -> checkMath();
				if ($report === true) {
					$this -> handedIn = true;
					$task = $this -> task;
					$this -> task -> editor -> notify('Текст по заданию '.$task -> show().' сдан.');
				} else {
					Yii::app() -> user -> setFlash('textHandIn',$report);
					$this -> addError('text',$report);
					return false;
				}
				break;
			case 'handInWithMistakes':
				$db = $this -> DBModel();
				if ((arrayString::removeRubbishFromString($this -> text) != arrayString::removeRubbishFromString($db -> text))||(!$db -> uid)) {
					Yii::app() -> user -> setFlash('textHandIn','Проведите проверку на уникальность перед повторной просьбой рассмотреть статью.');
					return false;
				} elseif ($db -> uniquePercent < 80) {
					Yii::app() -> user -> setFlash('textHandIn','Уникальность ниже 80%, что неприемлемо в любом случае.');
					return false;
				}
				$this -> QHandedIn = 1;
				$task = $this -> task;
				$this -> task -> editor -> notify('Поступила просьба рассмотреть текст по заданию '.$task -> show().'.');
				break;
			case 'accept':
				if (Yii::app() -> user -> checkAccess('administrateTask',['task' => $this -> task])) {
					$this -> accepted = 1;
					$this -> shingles = $this -> fastShingles() -> archive();
					$task = $this -> task;
					$task -> id_text = $this -> id;
					$task -> save();
					$task -> author -> notify('Текст по заданию '.$task -> show().' принят!');
				} else {
					return false;
				}
				break;
			case 'decline':
				if (Yii::app() -> user -> checkAccess('administrateTask',['task' => $this -> task])) {
					$this -> accepted = 0;
					$text = $this -> task -> createText($this);
					if (!$text -> save()) {
						$ar = $text -> getErrors();
					}
					$this -> task -> author -> notify('Текст по заданию '.$this -> task -> show().' отклонен. Более подробную информацию ищите в комментариях.');
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
				$this->uniquePercent = new CDbExpression('NULL');
				$this->uid = new CDbExpression('NULL');
				$this -> handedIn = 0;
				$this -> QHandedIn = 0;
			}
		}
		/**
		 * Считаем длину
		 */
		$toCount = preg_replace('/(\s|\r|\n)/u','',arrayString::removeSpecialChars($this -> text));
		$this -> length = strlen($toCount);
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
		 * Самая сложная проверка на уникальность
		 */
		//Если текст никак не изменился с момента последней проверки на уникальность
		if (arrayString::removeRubbishFromString($this -> text) == arrayString::removeRubbishFromString($this -> DBModel() -> text)) {
			//Тогда смотрим уникальность
			if ($this -> uniquePercent < self::MIN_UNIQUE) {
				$log('Уникальность ниже '.self::MIN_UNIQUE.'%, пожалуйста, исправьте.');
			}
		} else {
			$log('Проверка на уникальность не актуальна, пожалуйста, проведите проверку перед очередной попыткой сдать статью.');
		}

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
}
