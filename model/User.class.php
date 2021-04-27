<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 15:38
 */

class User
{
    use Singleton;

    private $user = [
        'id' => 0,
    ];

    private function assignUser($row)
    {
        $this->user['id'] = $row['id'];
        $this->user['login'] = $row['login'];
    }

    /**
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Проверка логина из сессии
     *
     * @return bool
     * @throws Exception
     */
    public function checkAuthLogin()
    {
        if (isset($_SESSION['login']) && isset($_SESSION['password'])) {
            $row = DB::getInstance()->QueryOne("SELECT * FROM users WHERE login=? and password=? LIMIT 1;", $_SESSION['login'], $_SESSION['password']);
            if (!$row) {
                unset($_SESSION['login']);
                unset($_SESSION['password']);
            } else {
                $this->assignUser($row);
                return true;
            }
        }
        return false;
    }

    /**
     * @param $message
     * @return bool
     * @throws Exception
     */
    public function checkAuthPost(&$message)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['login']) && $_POST['login'] != '') {
                $row = DB::getInstance()->QueryOne("SELECT * FROM users WHERE login=? LIMIT 1;", $_POST['login']);
                if (!$row) {
                    $message = "Неправильные реквизиты доступа!";
                    return false;
                } else {
                    if ($_POST['password'] && Config::get('secret_salt') . md5($_POST['password']) . Config::get('secret_salt') == $row['password']) {
                        $_SESSION['login'] = $row['login'];
                        $_SESSION['password'] = $row['password'];
                        $this->assignUser($row);
                        return true;
                    }
                }
            }
            $message = 'Неправильные реквизиты доступа!';
        }
        return false;
    }

    public function isAuthorized()
    {
        return isset($this->user) && $this->user['id'] > 0;
    }
}
