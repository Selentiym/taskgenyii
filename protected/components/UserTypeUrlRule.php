<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.07.2016
 * Time: 9:27
 */
class UserTypeUrlRule extends CBaseUrlRule
{
    public $connectionID = 'db';

    public function createUrl($manager,$route,$params,$ampersand)
    {
        if ($route==='cabinet')
        {
            $logged = User::logged();
            $user = $params['user'];
            if (!is_a($user,'User')) {
                if ($arg = $params['arg']) {
                    $user = User::model()->customFind($arg);
                }
            }
            if (!is_a($user, 'User')) {
                return 'cabinet';
            }
            if ($user != $logged) {
                return 'cabinet/'.$user -> username;
            }
        }
        return false;  // не применяем данное правило
    }

    public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
    {
        if (Yii::app() -> user -> isGuest) {
            return 'login/login';
        }
        if ($pathInfo == 'cabinet') {
            $user = User::logged();
            return array('cabinet/index','arg' => $user);
        }
        if (preg_match('%^cabinet(/(\w+))?$%', $pathInfo, $matches))
        {
            $arg = $matches[2];
            $user = User::model() -> customFind($arg);
            if (is_a($user, 'User')) {
                return array('cabinet/index','arg' => $user);
            }
            return array('cabinet/index','arg' => User::logged());
        }
        return false;  // не применяем данное правило
    }
}