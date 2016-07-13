<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 13.07.2016
 * Time: 10:27
 */
class TextRuApiHelper {
    /*
        2 функции для взаимодействия с API Text.ru посредством POST-запросов.
        Ответы с сервера приходят в формате JSON.
    */

    //-----------------------------------------------------------------------

    /**
     * Добавление текста на проверку
     *
     * @param string $text - проверяемый текст
     * @param string $user_key - пользовательский ключ
     * @param string $exceptdomain - исключаемые домены
     * @param string|bool $callbackUrl - обработчик
     *
     * @return string $text_uid - uid добавленного текста
     * @return int $error_code - код ошибки
     * @return string $error_desc - описание ошибки
     */
    public static function addPost($text, $callbackUrl = false)
    {
        $postQuery = array();
        $postQuery['text'] = $text;
        $postQuery['userkey'] = require_once('textRuCode.pss.php');
        // домены разделяются пробелами либо запятыми. Данный параметр является необязательным.
        //$postQuery['exceptdomain'] = "site1.ru, site2.ru, site3.ru";
        // Раскомментируйте следующую строку, если вы хотите, чтобы результаты проверки текста были по-умолчанию доступны всем пользователям
        $postQuery['visible'] = "vis_on";
        // Раскомментируйте следующую строку, если вы не хотите сохранять результаты проверки текста в своём архиве проверок
        //$postQuery['copying'] = "noadd";
        // Указывать параметр callback необязательно
        if ($callbackUrl) {
            //$postQuery['callback'] = $callbackUrl;
        }

        $postQuery = http_build_query($postQuery, '', '&');

        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, 'http://api.text.ru/post');
        curl_setopt($ch, CURLOPT_URL, Yii::app() -> createUrl('cabinet/log'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
        $json = curl_exec($ch);
        $errno = curl_errno($ch);

        // если произошла ошибка
        if (!$errno)
        {
            $resAdd = json_decode($json);
            if (isset($resAdd->text_uid))
            {
                $text_uid = $resAdd->text_uid;
            }
            else
            {
                $error_code = $resAdd->error_code;
                $error_desc = $resAdd->error_desc;
            }
        }
        else
        {
            $errmsg = curl_error($ch);
        }

        curl_close($ch);
        return array(
            'text_uid' => $text_uid,
            'error_code' => $error_code,
            'error_desc' => $error_desc
        );
    }

    /**
     * Получение статуса и результатов проверки текста в формате json
     *
     * @param string $text_uid - uid проверяемого текста
     * @param string $user_key - пользовательский ключ
     *
     * @return float $unique - уникальность текста (в процентах)
     * @return string $result_json - результат проверки текста в формате json
     * @return int $error_code - код ошибки
     * @return string $error_desc - описание ошибки
     */
    public static function getResultPost($uid)
    {
        $postQuery = array();
        $postQuery['uid'] = $uid;
        $postQuery['userkey'] = require_once('textRuCode.pss.php');
        // Раскомментируйте следующую строку, если вы хотите получить более детальную информацию в результатах проверки текста на уникальность
        //$postQuery['jsonvisible'] = "detail";

        $postQuery = http_build_query($postQuery, '', '&');

        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, 'http://api.text.ru/post');
        curl_setopt($ch, CURLOPT_URL, Yii::app() -> createAbsoluteUrl('cabinet/log'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
        $json = curl_exec($ch);
        $errno = curl_errno($ch);

        if (!$errno)
        {
            $resCheck = json_decode($json);
            if (isset($resCheck->text_unique))
            {
                $text_unique = $resCheck->text_unique;
                $result_json = $resCheck->result_json;
            }
            else
            {
                $error_code = $resCheck->error_code;
                $error_desc = $resCheck->error_desc;
            }
        }
        else
        {
            $errmsg = curl_error($ch);
        }

        curl_close($ch);
        return array(
            'text_unique' => $text_unique,
            'result_json' => $result_json,
            'error_code' => $error_code,
            'error_desc' => $error_desc,
            'errmsg' => $errmsg
        );
    }
    public static function ExpandedViewUrl($uid){
        return 'http://text.ru/antiplagiat/'.$uid;
    }
}