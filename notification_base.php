<?php

/**
 *
 */
class notification_base
{
    /**
     * @var string
     */
    public static $table;

    /**
     * @var array
     */
    public static $fields = array();

    /**
     * @param int|null $id
     */
    public function __construct($id = null)
    {
        if ($id) {
            return $this->findById($id);
        }
    }

    /**
     * @param $where
     * @return mixed
     */
    static public function findAll($where = null)
    {
        $results = array();
        $where = $where ? " WHERE $where" : '';
        foreach (self::$fields as $field) {
            $fields[] = "`$field`";
        }
        $query = "SELECT " . implode(', ', self::$fields) . " FROM `" . self::$table . "`" . $where;
        $result = $_ENV['DB']->Query($_ENV['config']['dbconn'], $query);
        while ($data = $_ENV['DB']->FetchArray($_ENV['config']['dbconn'], $result)) {
            $results[] = self::findById($data['id']);
        }
        return $results;
    }

    /**
     * @param $id
     * @return mixed
     */
    static public function findById($id)
    {
        $fields = array();
        foreach (self::$fields as $field) {
            $fields[] = "`$field`";
        }
        $query = "SELECT " . implode(', ', $fields) . " FROM `" . self::$table . "` WHERE id='" . (int)$id . "'";
        $result = $_ENV['DB']->Query($_ENV['config']['dbconn'], $query);
        $data = $_ENV['DB']->FetchArray($_ENV['config']['dbconn'], $result);

        $model = new get_called_class();
        foreach ($data as $k => $v) {
            $model->$k = $v;
        }
        return $model;
    }

    /**
     * @return mixed
     */
    public function save()
    {
        $fields = array();
        foreach (self::$fields as $field) {
            $value = $_ENV['DB']->Escape($this->$field);
            $fields[] = "`$field`='$value'";
        }
        $query = $this->id ? "UPDATE " : "INSERT INTO `" . self::$table . "` SET " . implode(', ', $fields);
        if ($this->id) {
            $query .= " WHERE `id`='" . (int)$this->id . "'";
        }
        return $_ENV['DB']->Query($_ENV['config']['dbconn'], $query);
    }

}
