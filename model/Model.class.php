<?php

abstract class Model {

    protected static $table = 'DUAL';

    public function deleteById($id) {
        return DB::getInstance()->QueryOne("DELETE FROM " . static::$table . " WHERE id=?", $id);
    }

    /**
     * @return array|bool
     * @throws Exception
     */
    public function getAll() {
        return DB::getInstance()->QueryMany("SELECT * from " . static::$table);
    }

    public function getById($id) {
        return DB::getInstance()->QueryOne("SELECT * FROM " . static::$table . " WHERE id=?", $id);
    }

    /**
     * Сохраняет массив в базу данных. Обновляет поля существующей или добавляет новую запись.
     *
     * @param array $record
     * @param array $errors
     * @throws Exception
     */
    public function save(&$record, &$errors = [])
    {

        // надо сохранить ид. даже пустой.
        $id = @$record['id'];
        // отфильтруем отсутствующие в БД поля и восстановим регистр названий полей
        $this->_restoreFieldNames($record);
        $record['id'] = $id;

        // проверка валидности данных
        $errors = array_merge($errors, $this->_checkParameters($record));
        if ($errors) return;

        if (!$id) {
            // новая запись
            self::_saveInsert($record);
        } else {
            // изменение записи
            self::_saveUpdate($record);
        }
    }

    /**
     * Обновляет поля существующего заказа
     * @param array $record
     * @throws Exception
     */
    private function _saveUpdate($record)
    {
        $dbt = $this->getById($record['id']);
        if (!$dbt) {
            throw new Exception('Запись с кодом ' . $record['id'] . ' либо не существует, либо нет прав доступа.');
        }

        $record = array_merge($dbt, $record);
        $fields = [];
        $params = [];
        foreach ($dbt as $key => $value) {
            if (strcmp($value, $record[$key])) {
                $fields[] = $key . '=?';
                $params[] = $record[$key];
            }
        }

        if (!$fields) return; // нет изменений

        $sql = "UPDATE " . static::$table . " SET " . implode(', ', $fields);
        $sql .= " WHERE id=? LIMIT 1;";
        array_unshift($params, $sql);
        array_push($params, $record['id']);
        DB::getInstance()->QueryOne(...$params);
    }

    /**
     * Создание нового заказа. По идее, прийти могут не все поля. Надо отфильтровать те, которых нет и сохранить остальные.
     *
     * @param array $record
     * @throws Exception
     */
    private function _saveInsert(&$record)
    {
        $defaultTable = [];
        $this->defaultTable($defaultTable);
        $record = array_merge($defaultTable, $record);

        $fields = [];
        $values = [];
        foreach ($defaultTable as $key => $value) {
            $fields[] = $key . '=?';
            $values[] = $record[$key];
        }
        $sql = "INSERT INTO " . static::$table . " SET " . implode(', ', $fields);
        array_unshift($values, $sql);
        DB::getInstance()->QueryOne(...$values);

        $record['id'] = DB::getInstance()->LastInsertId();
        if (empty($record['id'])) throw new Exception("Ошибка при создании записи");
    }

    /**
     * Проверяет поля на допустимые значения
     *
     * @param array $table
     * @return array Массив с ошибками
     * @throws Exception
     */
    protected function _checkParameters($table)
    {
        // тут можно побездельничать. При неверных ид выругается сама БД, т.к. будет нарушаться ее целостность и консистентность
        return [];
    }

    /**
     * Функция корректирет имена полей в запросе. Делает так, как в базе данных
     *
     * @param $table
     * @throws Exception
     */
    private function _restoreFieldNames(&$table)
    {
        $data = [];
        $defaultTable = [];
        $this->defaultTable($defaultTable);
        foreach ($defaultTable as $key => $value) {
            $lowerKey = mb_strtolower($key);
            if (isset($table[$key])) {
                $data[$key] = $table[$key];
            } elseif (isset($table[$lowerKey])) {
                $data[$key] = $table[$lowerKey];
            }
        }
        $table = $data;
    }

    /**
     * Создается пустой массив, содержащий поля из таблицы
     *
     * @param $default
     * @throws Exception
     */
    public function defaultTable(&$default)
    {
        $default = [];
        // получим поля
        $describe_table = DB::getInstance()->QueryMany("DESCRIBE " . static::$table . ";");
        foreach ($describe_table as $row) {
            if ($row['Field'] === 'id') continue;
            if ($row['Default'] === 'current_timestamp()') continue;
            $default[$row['Field']] = (($row['Default'] != '') ? $row['Default'] : '');
        }
    }
}
