<?php

class IndexController extends Controller
{
    public $view = 'index';
    public $title;

    function __construct()
    {
        parent::__construct();
    }
	
	function index(){
        $data = [];

        // информируем, что новая задача добавлена успешно
        if ( isset($_SESSION['added_new_todo']) ) {
            unset($_SESSION['added_new_todo']);
            $data['added_new_todo'] = 'success';
        }

        // сохранение и применение сортировки
        $data = array_merge($data, $this->processSort());

        // пагинация страницы, нормализация в диапазон
        list($currentPage, $recsPerPage) =$this->processPagination();

        $maxPageCount = Todo::getInstance()->paginate($recsPerPage, $currentPage);
        $currentPage = min($currentPage, $maxPageCount);

        $data['paginate']['page'] = $currentPage;
        $data['paginate']['pages'] = $maxPageCount;

        $data['todos'] = Todo::getInstance()->order($data['order_by'], $data['order_direction'])->getAll();

        return $data;
	}

	private function processPagination() {
        if (isset($_GET['page'])) {
            $_SESSION['page'] = max(1, intval($_GET['page']));
        } elseif (!isset($_SESSION['page'])) {
            $_SESSION['page'] = 1;
        }
        return [
            $_SESSION['page'],
            Config::get('records_per_page')
        ];
    }

    /**
     * @return array
     */
	private function processSort() {
        if (isset($_GET['order_by'])) {
            $_SESSION['order_by'] = $_GET['order_by'];
        } elseif (!isset($_SESSION['order_by'])) {
            $_SESSION['order_by'] = '';
        }
        if (isset($_GET['order_direction'])) {
            $_SESSION['order_direction'] = $_GET['order_direction'];
        } elseif (!isset($_SESSION['order_direction'])) {
            $_SESSION['order_direction'] = '';
        }
        return [
            'order_by' => $_SESSION['order_by'],
            'order_direction' => $_SESSION['order_direction']
        ];
    }
}
