<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.07.2016
 * Time: 11:21
 */
/**
 * @type Task $model
 */
$user = User::logged();
if ($model -> author == $user) {
    //echo "<a class='button' href='".Yii::app() -> createUrl('task/makeTask',["arg" => $model -> id])."'>Выполнить</a>";
}
//$this -> renderPartial('//pattern/'.$model -> pattern -> view, array('model' => $model));
$data = $model -> keyphrases;
?>


<style>
    tr td {
        background: #ffeb9c;
    }
    tr:first-child {
        font-weight:bold;
        text-align:center;
    }
    tr:first-child td {
        padding:10px;
        background: #cccccc;
    }
</style>


<p class="MsoNormal"><a name="_GoBack"></a><b><i>Тема статьи</i></b>: <span style="mso-ascii-font-family:
Calibri;mso-fareast-font-family:&quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;
mso-bidi-font-family:&quot;Times New Roman&quot;;color:#006100;mso-fareast-language:RU"><?php echo $model -> name; ?></span></p>

<p class="MsoNormal">В тексте не надо указывать номера телефонов, адреса клиник
    со страницы, кроме блока отзывов. Допускается использование названия клиники и улицы, где расположен
    диагностический центр.</p>

<p class="MsoNormal">Если длина статьи не задана в ТЗ (смотри в правом верхнем углу), то она ограничена объемом информации, которую необходимо разместить на странице, требованием уникальности и тошнотности.</p>



<ul>
    <li>Уникальность текста должна быть не менее <b>97%</b>. Первичная проверка проводится на данном ресурсе. Но текст также должен быть проверен в ручном режиме через программу <b>Advego Plagiatus</b> не менее <b></b>90%</b> в режиме глубокой проверки.</li>
    <li>Академическая тошнотность не более 9%</li>
    <li>Первый показатель в семантическом ядре не более 4</li>
    <li>Первый показатель в словах не более 4</li>
    <li>Перекрестная уникальность не более 3</li>
</ul>

<p class="MsoNormal">При написании строго следуйте требованиям тех. задания и
    соблюдайте количество ключей!</p>
<p>Текст должен иметь красивое форматирование. Пример:<img src="<?php echo Yii::app() -> baseUrl.'/images/structure_exapmle.png'; ?>"/></p>
<p class="MsoNormal">Для каждой статьи <b>пишите
        анонс</b> длинной в 200 символов – без пробелов.

    Анонс (description) - это краткое описание текста страницы. Он обязательно должен включать главный ключ
    и по возможности ключевые пассажи, показанные к употреблению в заголовках h2.

</p>



<p class="MsoNormal">Пояснение по таблице ключей:</p>

<table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0" width="729" style="width:546.7pt;border-collapse:collapse;mso-yfti-tbllook:1184;
 mso-padding-alt:0cm 5.4pt 0cm 5.4pt">
    <tbody><tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:14.7pt">
        <td width="535" nowrap="" valign="bottom" style="width:401.3pt;border:solid windowtext 1.0pt;
  mso-border-alt:solid windowtext .5pt;background:lightgrey;padding:0cm 5.4pt 0cm 5.4pt;
  height:14.7pt">
            <p class="MsoNormal" style="margin-bottom: 0.0001pt;"><b><span style="font-size: 10pt; font-family: Arial, sans-serif;">Фраза<o:p></o:p></span></b></p>
        </td>
        <td width="95" nowrap="" valign="bottom" style="width:71.5pt;border:solid windowtext 1.0pt;
  border-left:none;mso-border-top-alt:solid windowtext .5pt;mso-border-bottom-alt:
  solid windowtext .5pt;mso-border-right-alt:solid windowtext .5pt;background:
  lightgrey;padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" style="margin-bottom: 0.0001pt;"><b><span style="font-size: 10pt; font-family: Arial, sans-serif;">прямое<o:p></o:p></span></b></p>
        </td>
        <td width="99" nowrap="" valign="bottom" style="width:73.9pt;border:solid windowtext 1.0pt;
  border-left:none;mso-border-top-alt:solid windowtext .5pt;mso-border-bottom-alt:
  solid windowtext .5pt;mso-border-right-alt:solid windowtext .5pt;background:
  lightgrey;padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" st-yle="margin-bottom: 0.0001pt;"><b><span style="font-size: 10pt; font-family: Arial, sans-serif;">морфология<o:p></o:p></span></b></p>
        </td>
    </tr>
    <tr style="mso-yfti-irow:1;height:14.7pt">
        <td width="535" nowrap="" valign="bottom" style="width:401.3pt;border:solid #BFBFBF 1.0pt;
  border-top:none;mso-border-top-alt:solid #BFBFBF .5pt;mso-border-alt:solid #BFBFBF .5pt;
  background:#C6EFCE;padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" style="margin-bottom: 0.0001pt;"><span style="mso-ascii-font-family:Calibri;mso-fareast-font-family:
  &quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;mso-bidi-font-family:&quot;Times New Roman&quot;;
  color:#006100;mso-fareast-language:RU">купить розового слона со скидкой<o:p></o:p></span></p>
        </td>
        <td width="95" nowrap="" valign="bottom" style="width:71.5pt;border-top:none;
  border-left:none;border-bottom:solid #BFBFBF 1.0pt;border-right:solid #BFBFBF 1.0pt;
  mso-border-top-alt:solid #BFBFBF .5pt;mso-border-top-alt:solid #BFBFBF .5pt;
  mso-border-bottom-alt:solid #BFBFBF .5pt;mso-border-right-alt:solid #BFBFBF .5pt;
  background:#C6EFCE;padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" align="right" style="margin-bottom: 0.0001pt; text-align: right;"><span style="mso-ascii-font-family:Calibri;
  mso-fareast-font-family:&quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;
  mso-bidi-font-family:&quot;Times New Roman&quot;;color:#006100;mso-fareast-language:
  RU">1<o:p></o:p></span></p>
        </td>
        <td width="99" nowrap="" valign="bottom" style="width:73.9pt;border-top:none;
  border-left:none;border-bottom:solid #BFBFBF 1.0pt;border-right:solid #BFBFBF 1.0pt;
  mso-border-top-alt:solid #BFBFBF .5pt;mso-border-top-alt:solid #BFBFBF .5pt;
  mso-border-bottom-alt:solid #BFBFBF .5pt;mso-border-right-alt:solid #BFBFBF .5pt;
  background:#C6EFCE;padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" align="right" style="margin-bottom: 0.0001pt; text-align: right;"><span style="mso-ascii-font-family:Calibri;
  mso-fareast-font-family:&quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;
  mso-bidi-font-family:&quot;Times New Roman&quot;;color:#006100;mso-fareast-language:
  RU">&nbsp;</span></p>
        </td>
    </tr>
    <tr style="mso-yfti-irow:2;height:14.7pt">
        <td width="535" nowrap="" valign="bottom" style="width:401.3pt;border:solid #BFBFBF 1.0pt;
  border-top:none;mso-border-left-alt:solid #BFBFBF .5pt;mso-border-bottom-alt:
  solid #BFBFBF .5pt;mso-border-right-alt:solid #BFBFBF .5pt;background:#FFEB9C;
  padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" style="margin-bottom: 0.0001pt;"><span style="mso-ascii-font-family:Calibri;mso-fareast-font-family:
  &quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;mso-bidi-font-family:&quot;Times New Roman&quot;;
  color:#9C6500;mso-fareast-language:RU">слон розового цвета со стразами<o:p></o:p></span></p>
        </td>
        <td width="95" nowrap="" valign="bottom" style="width:71.5pt;border-top:none;
  border-left:none;border-bottom:solid #BFBFBF 1.0pt;border-right:solid #BFBFBF 1.0pt;
  mso-border-bottom-alt:solid #BFBFBF .5pt;mso-border-right-alt:solid #BFBFBF .5pt;
  background:#FFEB9C;padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" align="right" style="margin-bottom: 0.0001pt; text-align: right;"><span style="mso-ascii-font-family:Calibri;
  mso-fareast-font-family:&quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;
  mso-bidi-font-family:&quot;Times New Roman&quot;;color:#9C6500;mso-fareast-language:
  RU">1<o:p></o:p></span></p>
        </td>
        <td width="99" nowrap="" valign="bottom" style="width:73.9pt;border-top:none;
  border-left:none;border-bottom:solid #BFBFBF 1.0pt;border-right:solid #BFBFBF 1.0pt;
  mso-border-bottom-alt:solid #BFBFBF .5pt;mso-border-right-alt:solid #BFBFBF .5pt;
  background:#FFEB9C;padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" align="right" style="margin-bottom: 0.0001pt; text-align: right;"><span style="mso-ascii-font-family:Calibri;
  mso-fareast-font-family:&quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;
  mso-bidi-font-family:&quot;Times New Roman&quot;;color:#9C6500;mso-fareast-language:
  RU">1<o:p></o:p></span></p>
        </td>
    </tr>

    </tbody></table>

<p class="MsoNormal"><o:p>&nbsp;</o:p></p>

<p class="MsoNormal"><b>прямое</b> –
    означает, что ключ вписывается в неизменённом виде.</p>

<p class="MsoNormal"><b>морфология</b> –
    означает, что допустимо склонение слов в ключе и разбивка предлогами
    (знаками препинания и союзами недопустимо!) в пределах одного предложения. Порядок
    слов в пределах ключевой фразы меняться может. Приоритетно оставлять порядок слов, но если художественность текста требует, то можно поменять.</p>

<p class="MsoNormal"><b>1</b> – означает
    количество вхождений ключа в текст (без учета анонса)</p>

<p class="MsoNormal"><b>1 / 1</b> – означает,
    что ключ должен быть вписан один раз в неизменённом виде, второй раз в
    морфологии.</p>

<p class="MsoNormal"><b><span style="color:red">Главным ключом</span></b> называется тот, что в таблице
    выделен зеленым цветом. <b><span style="color:red">Главных ключей</span></b> может быть более одного.</p>


<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->3.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]-->Желательно <span style="color:red">главный ключ</span>
    помещать ближе к <b><i>началу статьи</i></b>.</p>
<h1>Пояснения по h1</h1>
<p>Он может быть только один на странице! Если в соответсвующем столбце таблицы указано использование заголовка h1, данный запрос должен быть помещен в заголовок h1 и, по возможности, должен быть использован в качестве названия статьи (в любом случае наверху!). Если более, чем один ключ должен быть обрамлен h1, то эти два ключа необходимо максимально сжато скомпоновать и использовать, по возможности, в заголовке статьи. Пример: мрт головного мозга, мрт медем => мрт головного мозга в медем </p>
<h2>Пояснения по h2</h2>
<p>Можно использовать несколько на странице. </p>

<p><b>Не используйте любое слово дважды в одном и том
        же предложении, особенно это касается слов из ключевых фраз.</b></p>



<p><b><i>Разбивайте текст на нумерованные/маркированные
            списки</i></b>. Выделяйте особо важные на ваш взгляд участки <i>курсивом.</i></p>

<p>Не прописываете главный ключ большее количество
    раз, чем это указано в задании.</p>

<p ><i><span>Равномерно распределяйте ключи по всему тексту</span></i>.</p>






<p class="MsoListParagraphCxSpLast" align="center" style="margin-bottom:18.0pt;
mso-add-space:auto;text-align:center"><b><span style="font-size:14.0pt;mso-bidi-font-size:11.0pt;line-height:107%">Структура</span></b></p>

<p class="MsoNormal">Статья должна содержать в себе следующие логические разделы:</p>

<?php $this -> renderPartial('//pattern/'.$model -> pattern -> view, array('model' => $model)); ?>

<p class="MsoNormal">Наличие структуры призвано стандартизировать статьи данного
    типа для удобства пользователя. Поэтому если Вы чувствуете, что получается
    неинтересно/неинформативно/некрасиво и вообще как-то не так с точки зрения
    человека, зашедшего на сайт в поисках информации, не стесняйтесь прерваться и
    обсудить свои вопросы или предложения с нами, особенно на первых этапах. Все вопросы можно
    писать во встроенный чат или скайп.
</p>
