<?php

class TodoController extends Controller
{
    public $view = 'todo';

    function __construct()
    {
        parent::__construct();
        $this->title = 'Новая задача';
    }

    function edit(){
        $data = [];
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (@$_POST['todo']['id']) {
                return $this->updateExists();
            }

            return $this->addNew();
        }

        if (@$_GET['id']) {
            $data['todo'] = Todo::getInstance()->getById($_GET['id']);
        }
        return $data;
    }


    /**
     * @return array
     */
    private function updateExists() {
        $errors = [];
        $data = [];

        if (User::getInstance()->isAuthorized()) {
            $oldTodo = Todo::getInstance()->getById($_POST['todo']['id']);
            if ($oldTodo) {
                if ($oldTodo['content'] !== $_POST['todo']['content']) {
                    $_POST['todo']['admin_edit'] = 1;
                }
                Todo::getInstance()->save($_POST['todo'], $errors);
            } else {
                $errors[] = 'Задача не найдена';
            }
        } else {
            $errors[] = 'Пользователь не авторизован!';
        }

        if (!$errors) {
            $data['success'] = 'Успешно сохранено!';
        }
        $data['errors'] = $errors;
        $data['todo'] = $_POST['todo'];
        return $data;
    }

    /**
     * @return array
     */
    private function addNew() {
        if (empty(@$_POST['todo'])) {
            return ['errors' => ['Пустые параметры']];
        }

        $errors = [];
        Todo::getInstance()->save($_POST['todo'], $errors);

        if (!$errors) {
            $_SESSION['added_new_todo'] = true;
            header('Location: /');
            exit;
        }

        $data['errors'] = $errors;
        $data['todo'] = $_POST['todo'];
        return $data;
    }

    public function update() {
        $this->ajax = true;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Неверный метод');
            }

            if (! User::getInstance()->isAuthorized()) {
                throw new Exception('Не авторизован!');
            }

            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);

            if (!isset($data['id']) || empty($data['id'])) throw new Exception('Ошибка id');
            if (!isset($data['status_code'])) throw new Exception('Нет значения status_code');
            $data['status_code'] = $data['status_code'] ? 1 : 0;

            Todo::getInstance()->save($data);
            return [];
        } catch (Exception $e) {
            return [ 'error' => $e->getMessage() ];
        }
    }
}
