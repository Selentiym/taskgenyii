<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.07.2016
 * Time: 9:35
 */
/**
 * @type Keyphrase[] $data
 */
?>
<table border="1">
    <tr><td>Фраза</td><td>Тег</td><td>Прямое</td><td>Морфология</td></tr>
    <?php
    $directCount = -1;
    $morphCount = -1;
    foreach ($data as $phrase) {
        if ($phrase -> direct) {
            ++ $directCount;
            $strict = $phrase -> direct;
            $idDirect = "id='direct$directCount'";
        } else {
            $strict = '';
            $idDirect = '';
        }
        if ($phrase -> morph) {
            ++ $morphCount;
            $morph = $phrase -> morph;
            $idMorph = "id='morph$morphCount'";
        } else {
            $morph = '';
            $idMorph = '';
        }
        $phrase_text = $phrase -> phrase;
        $trstyle = ($phrase -> direct > 0) ? 'background:#c6efce' : '' ;
        $tagName = $phrase -> tag;
        echo "<tr><td style='padding:5px;vertical-align:middle;text-align:left;$trstyle'>$phrase_text</td><td style='padding:5px;vertical-align:middle;text-align:center;$trstyle'>$tagName</td></td><td $idDirect data-mustHave='$strict' style='padding:5px;vertical-align:middle;text-align:center;$trstyle'>$strict</td><td $idMorph data-mustHave='$morph' style='padding:5px;vertical-align:middle;text-align:center;$trstyle'>$morph</td></tr>";
    }
    ?>
</table>
