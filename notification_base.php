<?php
/**
 * PHP Notifications
 *
 * @author Brett O'Donnell - cornernote@gmail.com
 * @copyright 2013, All Rights Reserved
 */

/**
 * Base class for notifications
 *
 */
class notification_base
{
    /**
     * Name of the database table
     *
     * @var string
     */
    public static $table;

    /**
     * Name of the primary key field
     *
     * @var string
     */
    public static $primaryKey = 'id';

    /**
     * Fields that will be loaded and saved
     *
     * @var array
     */
    public static $fields = array();

    /**
     * Construct a new model, or load a model from database
     *
     * @param null $pk
     */
    public function __construct($pk = null)
    {
        if ($pk) {
            return $this->findByPk($pk);
        }
    }

    /**
     * Find all rows matching the criteria
     *
     * @param $where
     * @return array
     */
    static public function findAll($where = null)
    {
		$class = get_called_class();
        $results = array();
        $where = $where ? " WHERE $where" : '';
        foreach ($class::$fields as $field) {
            $fields[] = "`$field`";
        }
        $query = "SELECT " . implode(', ', array_merge($class::$fields,array($class::$primaryKey))) . " FROM `" . $class::$table . "`" . $where;
        $result = $_ENV['DB']->Query($_ENV['config']['dbconn'], $query);
        while ($data = $_ENV['DB']->FetchArray($_ENV['config']['dbconn'], $result)) {
            $results[] = $class::findByPk($data['id']);
        }
        return $results;
    }

    /**
     * Find a single row with the selected pk
     *
     * @param $pk
     * @return array
     */
    static public function findByPk($pk)
    {
		$class = get_called_class();
        $fields = array();
        foreach ($class::$fields as $field) {
            $fields[] = "`$field`";
        }
        $query = "SELECT " . implode(', ', $fields) . " FROM `" . $class::$table . "` WHERE `" . $class::$primaryKey . "`='" . (int)$pk . "'";
        $result = $_ENV['DB']->Query($_ENV['config']['dbconn'], $query);
        $data = $_ENV['DB']->FetchArray($_ENV['config']['dbconn'], $result);

        $model = new $class();
        foreach ($data as $k => $v) {
            $model->$k = $v;
        }
        return $model;
    }

    /**
     * Save this model's attributes to the database
     *
     * @return mixed
     */
    public function save()
    {
		$class = get_called_class();
		$pk = $class::$primaryKey;
        $fields = array();
        foreach ($class::$fields as $field) {
			if (isset($this->$field)) {
				$value = $_ENV['DB']->Escape($_ENV['config']['dbconn'], $this->$field);
				$fields[] = "`$field`='$value'";
			}
        }
        $query = (isset($this->$pk) ? "UPDATE " : "INSERT INTO `") . $class::$table . "` SET " . implode(', ', $fields);
        if (isset($this->$pk)) {
            $query .= " WHERE `" . $class::$primaryKey . "`='" . (int)$this->$pk . "'";
        }
        return $_ENV['DB']->Query($_ENV['config']['dbconn'], $query);
    }

}
