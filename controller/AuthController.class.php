<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 17:15
 */

class AuthController extends Controller
{
    public $view = 'auth';
    public $title = 'Авторизация пользователя';

    public function index() {
        $message = '';
        $isAuthorized = User::getInstance()->checkAuthPost($message);

        return [
            'error_message' => $message,
            'is_auth' => $isAuthorized
        ];
    }

    public function logout() {
        session_destroy();
        header('Location: /auth');
        exit;
    }
}
