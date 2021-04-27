<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 27.04.2021
 * Time: 15:38
 */

class Todo extends Model
{
    use Singleton;
    protected static $table = 'todos';
    private $orderByList = ['user_name', 'email', 'status_code'];
    private $orderDirectionList = ['asc', 'desc'];
    private $orderBy = 'id';
    private $orderDirection = 'asc';

    private $limitFrom = 0;
    private $limitCount = 0;

    /**
     * Список полей только для админа
     * @var array
     */
    private $adminOnlyFillable = ['status_code', 'admin_edit'];

    public function order($orderBy = 'id', $orderDirection = 'asc') {
        if (in_array($orderBy, $this->orderByList)) {
            $this->orderBy = $orderBy;
        }
        if (in_array($orderDirection, $this->orderDirectionList)) {
            $this->orderDirection = $orderDirection;
        }

        return $this;
    }

    /**
     * Вернем максимальное кол-во страниц, для нормализации значения теущей страницы
     *
     * @param $recsPerPage
     * @param $currentPage
     * @return float
     */
    public function paginate($recsPerPage, $currentPage) {
        $row = DB::getInstance()->QueryOne("SELECT count(*) as cnt from " . static::$table);
        $countRecords = $row['cnt'];

        $pages = ceil($countRecords / $recsPerPage);

        $currentPage = min($pages, $currentPage);

        $this->limitFrom = ($currentPage - 1) * $recsPerPage;
        $this->limitCount = $recsPerPage;

        return $pages;
    }

    /**
     * @return string
     */
    private function makeLimitStatement() {
        if ($this->limitCount == 0) {
            return '';
        }
        return " LIMIT {$this->limitFrom},{$this->limitCount}";
    }

    public function getAll()
    {
        return DB::getInstance()->QueryMany("SELECT * from " . static::$table . " ORDER BY " . $this->orderBy . " " . $this->orderDirection . $this->makeLimitStatement());
    }

    /**
     * @param $id
     * @return array|bool
     * @throws Exception
     */
    public function getTodoById($id)
    {
        return DB::getInstance()->QueryOne("select * from" . static::$table . " where id=? limit 1", $id);
    }

    /**
     * @param array $fields
     * @return array
     */
    protected function _checkParameters($fields)
    {
        $errors = [];
        if (isset($fields['user_name']) && empty($fields['user_name'])) {
            $errors['user_name'][] = 'Пустое значение имени пользователя';
        }

        if (isset($fields['email']) && (empty($fields['email']) || !filter_var($fields['email'], FILTER_VALIDATE_EMAIL))) {
            $errors['email'][] = 'Неверное либо пустое значение email';
        }

        if (isset($fields['content']) && empty($fields['content'])) {
            $errors['content'][] = 'Пустой текст задачи';
        }

        return $errors;
    }

    public function save(&$record, &$errors = [])
    {
       if (!User::getInstance()->isAuthorized()) {
            // уберем поля, которые правит только админ
            foreach ($this->adminOnlyFillable as $field) {
                unset($record[$field]);
            }
        }

        parent::save($record, $errors);
    }
}
