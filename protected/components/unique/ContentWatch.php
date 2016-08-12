<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.08.2016
 * Time: 9:26
 */
class ContentWatch {
    /**
     * @var bool
     */
    public $success;
    /**
     * @var mixed[] an array with response from the server
     */
    public $response;
    /**
     * @var string $text - text to be checked
     */
    public $text;

    /**
     * ContentWatch constructor.
     * @param $text - text to be checked
     */
    public function __construct($text) {
        $this -> text = $text;
    }

    /**
     * @return mixed[] - array with post fields to the request
     */
    private function constructRequest () {
        return array(
            'key' => require_once('contentWatch.pss.php'), // ваш ключ доступа (параметр key) со страницы https://content-watch.ru/api/request/
            'text' => $this -> text,
            'test' => 0 // при значении 1 вы получите валидный фиктивный ответ (проверки не будет, деньги не будут списаны)
        );
    }

    public function sendRequest(){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this -> constructRequest());
        curl_setopt($curl, CURLOPT_URL, 'https://content-watch.ru/public/api/');
        // инициализируем дефолтные значения
        $defaults = array(
            'text' => '',
            'percent' => '100.0',
            'highlight' => array(),
            'matches' => array()
        );
        $this -> response = array_merge($defaults, json_decode(trim(curl_exec($curl)), TRUE));
        $this -> success = (!$this -> response ['error']);
        curl_close($curl);
    }

    /**
     * @return mixed[] - returns the response
     */
    public function summary(){
        return array_merge($this -> response,['success' => $this -> success]);
    }
}