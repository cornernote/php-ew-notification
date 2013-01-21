<?php

/**
 *
 */
class notification_base
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $fields = array();

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
    public function findAll($where = null)
    {
        $results = array();
        $where = $where ? " WHERE $where" : '';
        foreach ($this->fields as $field) {
            $fields[] = "`$field`";
        }
        $query = "SELECT " . implode(', ', $this->fields) . " FROM `" . $this->table . "`" . $where;
        $result = $_ENV['DB']->Query($_ENV['config']['dbconn'], $query);
        while ($data = $_ENV['DB']->FetchArray($_ENV['config']['dbconn'], $result)) {
            $results[] = $this->findById($data['id']);
        }
        return $this;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        $fields = array();
        foreach ($this->fields as $field) {
            $fields[] = "`$field`";
        }
        $query = "SELECT " . implode(', ', $fields) . " FROM `" . $this->table . "` WHERE id='" . (int)$id . "'";
        $result = $_ENV['DB']->Query($_ENV['config']['dbconn'], $query);
        $data = $_ENV['DB']->FetchArray($_ENV['config']['dbconn'], $result);
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function save()
    {
        $fields = array();
        foreach ($this->fields as $field) {
            $value = $_ENV['DB']->Escape($this->$field);
            $fields[] = "`$field`='$value'";
        }
        $query = $this->id ? 'UPDATE ' : 'INSERT INTO ';
        $query .= "`" . $this->table . "` SET ";
        $query .= implode(', ', $fields);
        if ($this->id) {
            $query .= " WHERE `id`='" . (int)$this->id . "'";
        }
        return $_ENV['DB']->Query($_ENV['config']['dbconn'], $query);
    }

}
