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
    со страницы. Допускается использование названия клиники и улицы, где расположен
    диагностический центр.</p>

<p class="MsoNormal"><b><i>Длина статьи не фиксируется явно</i></b>, она ограничена объемом
    информации, которую необходимо разместить на странице, требованием уникальности
    и тошнотности. Ниже об этом рассказано подробнее.</p>

<p class="MsoNormal"><o:p>&nbsp;</o:p></p>

<?php $this -> renderPartial('//pattern/keys', array('data' => $data)); ?>

<p class="MsoNormal"><o:p>&nbsp;</o:p></p>

<p class="MsoNormal">&nbsp;</p>

<p class="MsoNormal">Уникальность текста должна быть не менее 97% - проверяйте
    написанное на сервисе <a href="http://text.ru/antiplagiat">http://text.ru/antiplagiat</a></p>

<p class="MsoNormal">Прошу обратить внимание на данную функцию программы <b><span style="color:red">eTXTАнтиплагиат</span></b>.
    Проверка на рерайт сразу показывает, что статья писалась на основании одного
    источника легким рерайтом. Рерайт при проверке-приемке не пройдёт.</p>

<p class="MsoNormal"><!--[if gte vml 1]><v:shapetype
        id="_x0000_t75" coordsize="21600,21600" o:spt="75" o:preferrelative="t"
        path="m@4@5l@4@11@9@11@9@5xe" filled="f" stroked="f">
        <v:stroke joinstyle="miter"/>
        <v:formulas>
            <v:f eqn="if lineDrawn pixelLineWidth 0"/>
            <v:f eqn="sum @0 1 0"/>
            <v:f eqn="sum 0 0 @1"/>
            <v:f eqn="prod @2 1 2"/>
            <v:f eqn="prod @3 21600 pixelWidth"/>
            <v:f eqn="prod @3 21600 pixelHeight"/>
            <v:f eqn="sum @0 0 1"/>
            <v:f eqn="prod @6 1 2"/>
            <v:f eqn="prod @7 21600 pixelWidth"/>
            <v:f eqn="sum @8 21600 0"/>
            <v:f eqn="prod @7 21600 pixelHeight"/>
            <v:f eqn="sum @10 21600 0"/>
        </v:formulas>
        <v:path o:extrusionok="f" gradientshapeok="t" o:connecttype="rect"/>
        <o:lock v:ext="edit" aspectratio="t"/>
    </v:shapetype><v:shape id="_x0000_i1025" type="#_x0000_t75" style='width:294.75pt;
 height:161.25pt'>
        <v:imagedata src="file:///C:\Users\user\AppData\Local\Temp\msohtmlclip1\01\clip_image001.jpg"
                     o:title="Etxt Антиплагиат 2016-05-02 19"/>
    </v:shape><![endif]--></p>

<p class="MsoNormal">При написании строго следуйте требованиям тех. задания и
    соблюдайте количество ключей! Это позволит делать нашу работу слаженной и
    эффективной, без дополнительных доработок.</p>

<p class="MsoNormal">Для каждой статьи <b>пишите
        анонс</b> длинной в 200 символов – без пробелов. Анонс содержит одно вхождение
    ключа, прямое или морфологическое. Ключ в анонсе не учитывается при подсчете
    количества ключей в основном тексте. При заказе статьи на 4500 знаков, основной
    текст – это 4300 и анонс – 200 символов соответственно.</p>

<p class="MsoNormal"><o:p>&nbsp;</o:p></p>

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
            <p class="MsoNormal" style="margin-bottom: 0.0001pt;"><b><span style="font-size: 10pt; font-family: Arial, sans-serif;">морфология<o:p></o:p></span></b></p>
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
    <tr style="mso-yfti-irow:3;mso-yfti-lastrow:yes;height:14.7pt">
        <td width="535" nowrap="" valign="bottom" style="width:401.3pt;border:solid #BFBFBF 1.0pt;
  border-top:none;mso-border-left-alt:solid #BFBFBF .5pt;mso-border-bottom-alt:
  solid #BFBFBF .5pt;mso-border-right-alt:solid #BFBFBF .5pt;background:#FFEB9C;
  padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" style="margin-bottom: 0.0001pt;"><span style="mso-ascii-font-family:Calibri;mso-fareast-font-family:
  &quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;mso-bidi-font-family:&quot;Times New Roman&quot;;
  color:#9C6500;mso-fareast-language:RU">плюшевыйслоник для девочек розового
  цвета<o:p></o:p></span></p>
        </td>
        <td width="95" nowrap="" valign="bottom" style="width:71.5pt;border-top:none;
  border-left:none;border-bottom:solid #BFBFBF 1.0pt;border-right:solid #BFBFBF 1.0pt;
  mso-border-bottom-alt:solid #BFBFBF .5pt;mso-border-right-alt:solid #BFBFBF .5pt;
  background:#FFEB9C;padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" align="right" style="margin-bottom: 0.0001pt; text-align: right;"><span style="mso-ascii-font-family:Calibri;
  mso-fareast-font-family:&quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;
  mso-bidi-font-family:&quot;Times New Roman&quot;;color:#9C6500;mso-fareast-language:
  RU">или<o:p></o:p></span></p>
        </td>
        <td width="99" nowrap="" valign="bottom" style="width:73.9pt;border-top:none;
  border-left:none;border-bottom:solid #BFBFBF 1.0pt;border-right:solid #BFBFBF 1.0pt;
  mso-border-bottom-alt:solid #BFBFBF .5pt;mso-border-right-alt:solid #BFBFBF .5pt;
  background:#FFEB9C;padding:0cm 5.4pt 0cm 5.4pt;height:14.7pt">
            <p class="MsoNormal" align="right" style="margin-bottom: 0.0001pt; text-align: right;"><span style="mso-ascii-font-family:Calibri;
  mso-fareast-font-family:&quot;Times New Roman&quot;;mso-hansi-font-family:Calibri;
  mso-bidi-font-family:&quot;Times New Roman&quot;;color:#9C6500;mso-fareast-language:
  RU">или<o:p></o:p></span></p>
        </td>
    </tr>
    </tbody></table>

<p class="MsoNormal"><o:p>&nbsp;</o:p></p>

<p class="MsoNormal"><b>прямое</b> –
    означает, что ключ вписывается в неизменённом виде.</p>

<p class="MsoNormal"><b>морфология</b> –
    означает, что допустимо склонение слов в ключе и разбивка предлогами или
    знаками препинания в пределах одного предложения. Порядок слов меняться не
    должен.</p>

<p class="MsoNormal"><b>1</b> – означает
    количество вхождений ключа в текст (без учета анонса)</p>

<p class="MsoNormal"><b>1 / 1</b> – означает,
    что ключ должен быть вписан один раз в неизменённом виде, второй раз в
    морфологии.</p>

<p class="MsoNormal"><b>или / или</b> –
    означает, что ключ должен быть вписать один раз в неизменённом или в склонении.</p>

<p class="MsoNormal"><b><span style="color:red">Главным ключом</span></b> называется тот, что в таблице
    выделен зеленым цветом. <b><span style="color:red">Главных ключей</span></b> может быть более одного.</p>

<p class="MsoListParagraphCxSpFirst" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->1.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]-->Текст должен быть лаконичным и информативным, <b><i>никакой
            воды</i></b>.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->2.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]-->Пишите утверждающие тексты. </p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->3.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]-->Желательно <span style="color:red">главный ключ</span>
    помещать ближе к <b><i>началу статьи</i></b>.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->4.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]-->Ключевые слова, в случае морфологического
    использования, можно склонять, разбивать знаками препинания или предлогами.
    Главное придерживаться правил русского языка и читаемости.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->5.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]--><b><i>Никакого прямого обращения к читателю</i></b>
    «<span style="color:red">Вы можете выбрать</span>», «<span style="color:red">Для
Вас это будет</span>», «<span style="color:red">Если Вы решили</span>» и т.д.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->6.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]-->Не начинайте статью или анонс с фразы «Эта
    статья», «В этой статье» и т.д. </p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->7.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]-->Не заканчивайте тексты абзацами-предложениями
    «Итого», «Вывод», «Ну и в заключение» и т.д.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->8.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]--><b><i>Не используйте</i></b> всевозможные
    «фразы-паразиты»: «<span style="color:red">Также стоит отметить</span>», «<span style="color:red">Несмотря на то, что</span>», «<span style="color:red">Более
того</span>», «<span style="color:red">Стоит отметить</span>», «<span style="color:red">Для того чтобы</span>», «<span style="color:red">Благодаря
тому</span>» - примеры неправильного и правильного написания:</p>

<p class="MsoListParagraphCxSpMiddle"><i><span style="color:red">Неправильно: Для того, чтобы сделать томографию с контрастом,
необходима подготовка…</span><o:p></o:p></i></p>

<p class="MsoListParagraphCxSpMiddle"><i><span style="color:#00B050">Правильно: Прохождение томографии с контрастом требует подготовки…<o:p></o:p></span></i></p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->9.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span><!--[endif]-->Никаких прилагательных: «Лучший»,
    «Великолепный», «Очень удобный» и т.д.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->10.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]-->Не используйте любое слово дважды в одном и том
    же предложении, особенно это касается слов из ключевых фраз.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->11.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]-->Пишите небольшими предложениями, а не одно на
    целый абзац. Короткие предложения легче читаются и воспринимаются.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->12.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]-->Разбивайте тексты на абзацы по 250-450 символов
    с подзаголовками (по 2-4 предложения).</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->13.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]--><b><i>Разбивайте текст на нумерованные/маркированные
            списки</i></b>. Выделяйте особо важные на ваш взгляд участки <i>курсивом.</i></p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->14.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]-->Не прописываете главный ключ большее количество
    раз, чем это указано в задании.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->15.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]-->Выделяйте основной ключ в любой форме вхождения <b><span style="color:red">красным цветом</span></b>,
    остальные ключи <b><span style="color:#00B050">зеленым
цветом</span></b>. </p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->16.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]--><b><i>Не используйте главный ключ для
            подзаголовков</i></b>.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->17.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]--><i><span style="color:red">Равномерно распределяйте ключи по всему тексту, <b>особенно главный</b></span></i>.</p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->18.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]-->Проверяйте «тошноту» текстов. Онлайн сервис <a href="http://advego.ru/text/seo/">http://advego.ru/text/seo/</a> - верхние
    показатели в окнах <b><i>Семантическое ядро</i></b> и <b><i>Слова</i><span style="color:red">не должны
превышать 4%.</span>
        Академическая тошнота документа<span style="color:red"> не более 9%.</span></b></p>

<p class="MsoListParagraphCxSpMiddle" style="text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]-->19.<span style="font-stretch: normal; font-size: 7pt; font-family: 'Times New Roman';">&nbsp;&nbsp;
</span><!--[endif]-->Законченный текст сдавайте в документе Word.</p>

<p class="MsoListParagraphCxSpMiddle"><o:p>&nbsp;</o:p></p>

<p class="MsoListParagraphCxSpLast" align="center" style="margin-bottom:18.0pt;
mso-add-space:auto;text-align:center"><b><span style="font-size:14.0pt;mso-bidi-font-size:11.0pt;line-height:107%">Структура<o:p></o:p></span></b></p>

<p class="MsoNormal">Статья должна содержать в себе следующие логические разделы:</p>

<?php $this -> renderPartial('//pattern/'.$model -> pattern -> view, array('model' => $model)); ?>

<p class="MsoNormal">Наличие структуры призвано стандартизировать статьи данного
    типа для удобства пользователя. Поэтому если Вы чувствуете, что получается
    неинтересно/неинформативно/некрасиво и вообще как-то не так с точки зрения
    человека, зашедшего на сайт в поисках информации, не стесняйтесь прерваться и
    обсудить свои вопросы или предложения с нами, особенно на первых этапах. Пока
    что все работы и/или вопросы отсылайте на почту <a href="mailto:bondartsev.nikita@gmail.com"><span lang="EN-US">bondartsev</span>.<span lang="EN-US">nikita</span>@<span lang="EN-US">gmail</span>.<span lang="EN-US">com</span></a>. Либо же в скайпе/в контакте,
    как удобнее. ВК: <a href="https://vk.com/bondartsev">https://vk.com/bondartsev</a>
    скайп: <span lang="EN-US">Selentiym</span>.</p>
