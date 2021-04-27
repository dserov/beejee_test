<?php

class App
{
    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function init()
    {
        session_start();
        date_default_timezone_set('Europe/Moscow');
        DB::getInstance()->Connect(Config::get('db_user'), Config::get('db_password'), Config::get('db_base'));

        // обработка запроса
        self::web();
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected static function web()//РОУТЕР!!!
    {
        list($pageName, $methodName) = self::parseRequestUri();

        // проверка авторизации по сессии
        User::getInstance()->checkAuthLogin();

        $controllerName = array_reduce(explode('_', $pageName), function ($a, $b) {
                return $a . ucfirst($b);
            }, '') . 'Controller'; //IndexController
        $controller = new $controllerName();

        //Ключи данного массива доступны в любой вьюшке
        //Массив data - это массив для использования в любой вьюшке
        $data = [
            'content' => $controller->$methodName(),
            'page_title' => $controller->title,
            'is_auth' => User::getInstance()->isAuthorized(),
            'page_name' => $pageName,
        ];

        // вывод результата
        if ($controller->ajax) {
            $result_code = (isset($data['content']['error'])) ? 400 : 200;
            Http::response($result_code, $data['content']);
        } else {
            $view = $controller->view . '/' . $methodName . '.html';
            $loader = new \Twig\Loader\FilesystemLoader(Config::get('path_templates'));
            $twig = new \Twig\Environment($loader);
            echo $twig->render($view, $data);
        }
    }

    protected static function parseRequestUri()
    {
        $pageName = 'index';
        $methodName = 'index';
        $url = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        array_shift($url);
        if (!empty($url[0])) {
            $pageName = $url[0];//Часть имени класса контроллера
            if (isset($url[1])) {
//                if (is_numeric($url[1])) {
//                    $_GET['id'] = $url[1];
//                } else {
                $methodName = $url[1];// имя метода
//                }
//                if (isset($url[2])) {//формальный параметр для метода контроллера
//                    $_GET['id'] = $url[2];
//                }
            }
        }

        return [
            $pageName,
            $methodName
        ];
    }
}
