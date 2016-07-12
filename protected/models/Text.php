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
 *
 * The followings are the available model relations:
 * @property Task $task
 */
class Text extends Commentable {
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
	 */
	public function analyze($post){
		$rez = array();
		$text = $post['text'];
		$this -> text = $text;
		$text = new textString(strip_tags($text));

		$i = -1;
		foreach($this -> task -> keyphrases as $phr){
			if ($phr -> direct > 0) {
				++$i;
				$rez['phrs']['direct'][] = $text->lookForLiteral($phr -> phrase);
			}
		}
		$i = -1;
		foreach($this -> task -> keyphrases as $phr){
			if ($phr -> morph > 0) {
				++$i;
				$rez['phrs']['morph'][] = $text->lookForSentenced(new wordSet($phr->phrase, $i));
			}
		}
		$rez['text'] = $text -> getToPrint();


		echo json_encode($rez, JSON_PRETTY_PRINT);
	}
	/**
	 * Основная функция интерфейса для авторов.
	 * Считает ключевые слова, получает разные статистики из api, проверяет уникальность.
	 * Попутно сохраняет временные изменения в тексте вместе с некоторыми параметрами.
	 *
	 * Весь вывод идет в виде json-объекта.
	 * @var mixed[] $post
	 */
	public function analyzeOld($post){
		$rez = array();
		$text = $post['text'];
		$this -> text = $text;
		$text = new sentenceString(strip_tags($text));
		$i = -1;
		foreach($this -> task -> keyphrases as $phr){
			//++ $i;
			$rez['phrs'][] = $text -> lookFor(new arrayString($phr -> phrase));
		}



		echo json_encode($rez, JSON_PRETTY_PRINT);
	}

	/**
	 * Возвращает параметры текста, которые нужно контролировать по ТЗ.
	 * Академическая тошнота, первая строка в семантическом ядре и словах.
	 */
	public function seoStat($post){
		$out = '';
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
		//Выводим результат
		echo json_encode($rez, JSON_PRETTY_PRINT);
	}
}
