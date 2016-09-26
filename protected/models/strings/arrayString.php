<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 11:14
 */
//require_once('Stemmer.php');
class arrayString {
    const prepositions = array(
        "возле","около","у","в","для", "на", "по", "со", "из", "от", "до", "без", "над", "под", "за", "при", "после", "во","у"
    );
    const stops = array(
        "не", "же", "то", "бы", "всего", "итого", "даже", "да", "нет",
        "или", "но", "дабы", "затем", "потом", "коли", "лишь только",
        "как", "так", "еще", "тот", "откуда", "зачем", "почему", "значительно",
        "он", "мы", "его", "вы", "вам", "вас", "ее", "что", "который","которая", "которых", "которое", "их", "все", "они", "я", "весь", "мне", "меня", "таким", "весь", "всех",
        "кб", "мб", "дн", "руб", "ул", "кв", "дн", "гг",
        "ой", "ого", "эх", "браво", "здравствуйте", "спасибо", "извините",
        "что-то", "какой-то", "где-то", "как-то", "зачем-то", "из-за", "дальше", "ближе", "раньше", "позже", "когда-то",
        "всего","почти","примерно","около","где-то", "порядка",
        "очень", "минимально", "максимально", "абсолютно", "огромный", "предельно", "сильно", "слабо", "наиболее", "наименьшее", "самый",
        "красивый", "мягкий", "удобный", "дорогой", "эффективный",
        "масса ярких впечатлений","в лучших традициях","ударными темпами","трезвый взгляд","шаг за шагом","так или иначе","сплошь и рядом","направо и налево","туда и сюда","доверие клиентов","решать задачи бизнеса","расширить географию продаж","в настоящее время","в наши дни","в это столетие","в нашем веке","век высоких технологий","сегодня","сейчас",
        "является", "есть", "иметь", "хотеть", "содержаться", "существует",
        "осуществлять", "оказывается",
        "можно продолжать", "можно заказать",
        //"Интернет","без смс","авторизуйтесь","войдите","введите имя","сайт","закрыть окошко","вопросы","ожидайте ответа","прайс-лист","заказ","меню","на этой странице","форма внизу страницы","нажмите на кнопку","напишите письмо","кликните здесь","зарегистрируйтесь","перейти","закрыть","получить заказ","далее","следующий","имя","бесплатно","без пароля","без регистрации","без пароля"
    );
    /**
     * @var bool $ready - показывает, подготовлен ли текст к сравнению по корням.
     */
    public $ready = false;
    /**
     * @var string $text
     */
    public $text = '';
    /**
     * @var string[] $stems - массив корней. Важен порядок!
     */
    public $stems;
    /**
     * @var string $initial
     */
    public $initial;
    /**
     * arrayString constructor.
     * @param string $string - the text which will be converted into array
     */
    public function __construct($string){
        $this -> initial = $string;
        $this -> text = $string;
    }
    public static function removeSpecialChars($str) {
        $str = strip_tags($str);
        $str = preg_replace('/&[a-z]+;/',' ',$str);
        $str = trim(preg_replace ("/[\r]/u", " ", $str));
        //Удаляем лишние пробелы и переносы строк
        $str = preg_replace ("/(?<=\w)\s+(?=\w)/u", " ", $str);
        return $str;
    }
    public static function removeRubbishFromString($str){
        $str = self::removeSpecialChars($str);
        //Чтобы санкт-петербург не сливался в одно слово
        $str = str_replace('-',' ',$str);
        //Оставляем только буквы и пробелы
        $str = preg_replace('/[^\s\w]/u', ' ', $str);
        //удаляем стоп-слова
        $str = preg_replace('/(^|\s)(возле|около|в|у|для|на|по|со|из|от|до|без|над|под|за|при|после|во|не|же|то|бы|всего|итого|даже|да|нет|или|но|дабы|затем|потом|коли|лишь только|как|так|еще|тот|откуда|зачем|почему|значительно|он|мы|его|вы|вам|вас|ее|что|который|которая|которых|которое|их|все|они|я|весь|мне|меня|таким|весь|всех|кб|мб|дн|руб|ул|кв|дн|гг|ой|ого|эх|браво|здравствуйте|спасибо|извините|что-то|какой-то|где-то|как-то|зачем-то|из-за|дальше|ближе|раньше|позже|когда-то|всего|почти|примерно|около|где-то|порядка|очень|минимально|максимально|абсолютно|огромный|предельно|сильно|слабо|наиболее|наименьшее|самый|красивый|мягкий|удобный|дорогой|эффективный|масса ярких впечатлений|в лучших традициях|ударными темпами|трезвый взгляд|шаг за шагом|так или иначе|сплошь и рядом|направо и налево|туда и сюда|доверие клиентов|решать задачи бизнеса|расширить географию продаж|в настоящее время|в наши дни|в это столетие|в нашем веке|век высоких технологий|сегодня|сейчас|является|есть|иметь|хотеть|содержаться|существует|осуществлять|оказывается|можно продолжать|можно заказать)(?=$|\s)/ui','',$str);
        return $str;
    }
    public static function leaveOnlyLetters($str){
        $str = self::removeSpecialChars($str);
        $str = preg_replace('/\s+/','',$str);
        return $str;
    }
    public function removeRubbish(){
        $this -> text = self::removeRubbishFromString($this -> text);
    }
    public function findStems(){
        $this -> stems = Stemmer::getInstance() -> stem_words(array_filter(array_map('trim',explode(' ',$this -> text))));
    }
    public function forgetWordForms(){
        if (!$this -> stems) {
            $this -> findStems();
        }
        $this -> text = implode(' ',$this -> stems);
    }
    public function stemmedText(){
        if (!$this -> ready) {
            $this -> prepare();
        }
        return $this -> text;
    }
    public function prepare(){
        $this -> removeRubbish();
        $this -> forgetWordForms();
        $this -> ready = true;
    }

    /**
     * @param arrayString $needle - string to be found in the current one
     * @return int - number of needles in this string
     */
    public function lookFor(arrayString $needle) {
        $findText = $needle -> stemmedText();
        $count = 0;
        $this -> text = str_replace($findText,'<span style="color:green;font-weight:bold">'.$findText.'</span>',$this -> stemmedText(),$count);
        return $count;
    }
    public function lookForUnordered(arrayString $needle){
        $this -> prepare();
        $needle -> prepare();
        return (int) (count(array_intersect($this ->stems, $needle -> stems)) == count($needle -> stems));
    }
    public function lookForUnorderedHighlight(arrayString $needle){
        $this -> prepare();
        $needle -> prepare();

    }
    public function dist(arrayString $s){
        $this -> prepare();
        $s -> prepare();
        $bigger = $this;
        $smaller = $s;
        if (count($smaller -> stems) > count($bigger -> stems)) {
            $bigger = $s;
            $smaller = $this;
        }
        $max = count($bigger -> stems);
        if (!$max) {$max = 1;}
        return ($max - count(array_intersect($bigger -> stems, $smaller -> stems)))/$max;
    }

    /**
     * @return string - the standard form of the first word
     */
    public function lemma(){
        return Stemmer::getInstance() -> lemmatize(current(array_filter(array_map('trim',explode(' ',$this -> initial)))));
    }
}