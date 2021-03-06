<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.07.2016
 * Time: 10:34
 */
class word {

    const stopsPattern = '/(^|\s)(возле|около|в|у|для|на|по|со|из|от|до|без|над|под|за|при|после|во|не|же|то|бы|всего|итого|даже|да|нет|или|но|дабы|затем|потом|коли|лишь только|как|так|еще|тот|откуда|зачем|почему|значительно|он|мы|его|вы|вам|вас|ее|что|который|которая|которых|которое|их|все|они|я|весь|мне|меня|таким|весь|всех|кб|мб|дн|руб|ул|кв|дн|гг|ой|ого|эх|браво|здравствуйте|спасибо|извините|что-то|какой-то|где-то|как-то|зачем-то|из-за|дальше|ближе|раньше|позже|когда-то|всего|почти|примерно|около|где-то|порядка|очень|минимально|максимально|абсолютно|огромный|предельно|сильно|слабо|наиболее|наименьшее|самый|красивый|мягкий|удобный|дорогой|эффективный|масса ярких впечатлений|в лучших традициях|ударными темпами|трезвый взгляд|шаг за шагом|так или иначе|сплошь и рядом|направо и налево|туда и сюда|доверие клиентов|решать задачи бизнеса|расширить географию продаж|в настоящее время|в наши дни|в это столетие|в нашем веке|век высоких технологий|сегодня|сейчас|является|есть|иметь|хотеть|содержаться|существует|осуществлять|оказывается|можно продолжать|можно заказать)(?=$|\s)/ui';

    public $toPrint = '';
    public $stem = '';
    public $used = false;
    public function __construct($word) {
        $this -> toPrint = $word;
        $word = trim($word);
        if ($word) {
            if (preg_match("/" . textString::quatation . "/ui", $word)) {
                $this->stem = $word;
                return;
            }
            if (preg_match("/^(и|но|а|или)$/ui",$word)) {
                $this->stem = $word;
                return;
            }
            if (strlen($word) > 1) {
                //удаляем стоп-слова
                $word = preg_replace(word::stopsPattern, '', $word);
                if ($word) {
                    $this->stem = Stemmer::getInstance()->stem_word($word);
                }
            }
        }
    }
    public function makeUse($color = 'green', $param = ''){
        $this -> used = true;
        $this -> toPrint = '<span style="background-color:'.$color.'" data-param="'.$param.'">'.$this -> toPrint.'</span>';
    }
    public function getToPrint(){
        return $this -> toPrint;
    }
}