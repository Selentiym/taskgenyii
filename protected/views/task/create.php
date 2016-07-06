<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2016
 * Time: 10:02
 */

require_once('classes/arrayString.php');
$data = $_POST;

//Получили список поисковых фраз
$phr = array_map(function($el){
    $temp = array_map('trim',preg_split("/\t/",trim($el)));
    if ($temp[0]) {
        return new phraseString($temp[0],$temp[5]);
    }
    return false;
},preg_split("/\r\n/", $data['phrases']));

//Сортируем фразы по убыванию частоты точных вхождений
usort($phr,function($el1, $el2){
    if ($el1 -> num > $el2 -> num) return -1;
    if ($el1 -> num < $el2 -> num) return 1;
    return 0;
});

//Получили список кластеризованных слов.
$cluster = array_map(function($el){
    return array_map('trim',preg_split("/\t/",trim($el)));
},array_map('trim',preg_split("/\r\n/",$data['cluster'])));
$max = -1;
//Будет хранить корни кластеризованных слов
$keywords = array();
foreach ($cluster as $word) {
    if ($word[1] > $max) {
        $max = $word[1];
    }
    //Обрезаем все после запятой
    if ($pos = strpos($word[0],',')) {
        $temp = substr($word[0], 0, $pos);
    } else {
        $temp = $word[0];
    }
    $temp = new metricString($temp, $word[1]);
    $temp -> prepare();
    //Сохраняем клчевик, только если он удобный
    if ($temp -> text) {
        $keywords[$temp->text] = array('left' => 1, 'obj' => $temp);
    }
}

$wordArr = reset($keywords);
$word = $wordArr['obj'];
//Использованные фразы
$used = array();

//Перебираем наиболее часто встречающиеся слова
while ($word -> num ) {
    if ($wordArr['left'] > 0) {
        $found = false;
        //Пробегаем все фразы.
        foreach ($phr as $p) {
            //Не смотрим на нечеловечевские фразы
            if ($p -> num < 2) break;
            if ($p->lookForUnordered($word)) {
                $found = $p;
                //Теперь проверяем, насколько близко друг к другу находятся уже найденные фразы и новая
                foreach ($used as $u) {
                    //Если расстояние мало, то новая строка не подходит
                    if ($u -> dist($p) < 0.3) {
                        $found = false;
                        break;
                    }
                }
            }

        }
        //Если нашлась фраза, содержащая нужно слово, то используем ее
        if ($found) {
            $used[] = $found;
            foreach ($found->stems as $stem) {
                $cur = $keywords[$stem];
                //Если было изначально слово в ключевых, то уменьшаем его количество на единицу
                if ($cur['obj']) {
                    $keywords[$stem]['left']--;
                }
            }
        }
    }
    $wordArr = next($keywords);
    $word = $wordArr['obj'];
}
//var_dump($keywords);
//var_dump($cluster);

/*$phr = array_map(function($el) {
    $temp = trim($el);
    return array_map('trim', explode('\t',$el));
},explode('\r',$data['phrases']));*/
//var_dump($phr);
?>
<link rel="stylesheet" href="css/usergen.css"/>
<script src="js/jquery.min.js"></script>
<script src="js/underscore.js"></script>
<script src="js/Classes.js"></script>
<script>
    $(document).ready(function(){
        Word.prototype.container = $("#wordsCont");
        Phrase.prototype.container = $("#phrasesCont");
        <?php
            foreach($keywords as $obj) {
                $obj = $obj['obj'];
                $stems = json_encode($obj -> stems, JSON_PRETTY_PRINT);
                echo "new Word('{$obj -> initial}',{stems:$stems,num:{$obj -> num}});".PHP_EOL;
            }
            foreach ($used as $p) {
                $stems = json_encode($p -> stems,JSON_PRETTY_PRINT);
                echo "new Phrase('$p->initial',{stems:$stems})".PHP_EOL;
            }
        ?>
    });
</script>
<form method="post" action="tasklist.php" id="phrasesCont">

    <input type="submit" value="Составить ТЗ"/>
</form>

<table id="keywords">
    <thead>
    <tr><td>Слово</td><td>Псевдокорень</td><td>Количество</td><td>Важность</td></tr>
    </thead>
    <tbody id="wordsCont">

    </tbody>
</table>
