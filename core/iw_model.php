<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Extension of core CI_Model
 *
 */
class IW_Model extends CI_Model {

    // Default Options:
    protected $auto_increment = true;
    private $tablename = null;
    private $primary_key = null;
    public $id = null;
    public $values = array();
    public $validation_rules = array();

    public function __construct($tablename = null, $primary_key = 'id') {
        if (!empty($tablename)) {
            $this->set_tablename($tablename);
        }

        $this->set_primary_key($primary_key);

        parent::__construct();
    }

    function set_values($values = array()) {
        if (isset($values[$this->get_primary_key()])) {
            $this->id = $values[$this->get_primary_key()];
        }
        
        unset($values['submit_button']);

        $this->values = array_merge($this->values, $values);

        return $this;
    }
    
    function get_value($column = '') {
        return isset($this->values[$column]) ? $this->values[$column] : null;
    }
    
    protected function set_tablename($tablename) {
        $this->tablename = $tablename;
    }

    public function get_tablename() {
        if (null === $this->tablename) {
            //The exception will block the use of functions that require tablename being set.
            throw new Exception('Must declare the table name in the subclass.');
        }

        return $this->tablename;
    }

    protected function set_primary_key($primary_key) {
        $this->primary_key = $primary_key;
    }

    public function get_primary_key() {
        if (null === $this->primary_key) {
            //The exception will block the use of functions that require primary_key being set.
            throw new Exception('Must declare the primary key in the subclass.');
        }

        return $this->primary_key;
    }

    public function _create($data, $return_id = true) {
        if (true === $this->auto_increment && array_key_exists($this->get_primary_key(), $data)) {
            unset($data[$this->get_primary_key()]);
        }

        $this->db->insert($this->get_tablename(), $data);

        $id = $this->db->insert_id();
        $this->set_values(array($this->get_primary_key() => $id));

        if ($return_id) {
            return $id;
        }
    }

    public function _get($index_by = null) {
        $index_by = (null === $index_by) ? $this->primary_key : $index_by;

        $query = $this->db->get($this->get_tablename());
        $result = $query->result_array();

        if ($index_by) {
            $result = $this->index_rows($query->result_array(), $index_by);
        } else {
            $result = $query->result_array();
        }

        return $result;
    }
    
    public function get_all($index_by = null) {
        return $this->_get($index_by);
    }

    public function get_one($set_values = false) {
        $row = $this->_get();

        if (!empty($row)) {
            $row = reset($row);
        }

        if (true === $set_values) {
            $this->set_values($row);
        }

        return $row;
    }

    function get_for_select($column_name = 'name', $order_type = 'ASC', $index_by = null, $distinct = true) {

        $index_by = empty($index_by) ? $this->get_primary_key() : $index_by;

        if ($distinct) {
            $this->db->distinct();
        }

        $this->db->order_by($column_name, $order_type);
        $rows = $this->_get($index_by);

        $result = array();
        foreach ($rows as $row) {
            $result[$row[$index_by]] = $row[$column_name];
        }

        return $result;
    }

    public function get_by_id($id, $set_values = false) {
        $this->db->where($this->get_primary_key(), $id);
        $row = $this->get_one($set_values);

        return $row;
    }

    public function _update($data, $verify_where = true) {
        if (empty($this->db->ar_where) && $verify_where) {
            throw new Exception('You cannot update without specifying a where clause.');
        }

        if (true === $this->auto_increment && array_key_exists($this->get_primary_key(), $data)) {
            unset($data[$this->get_primary_key()]);
        }

        $result = $this->db->update($this->get_tablename(), $data);

        return $this->db->affected_rows();
    }

    function save() {
        if (!empty($this->id)) {
            $result = $this->update(array($this->get_primary_key() => $this->id));
            //$result is rows updated
        } else {
            //$result is id created
            $this->id = $result = $this->insert($this->values);
        }
        return $result;
    }

    function update($primary_key = NULL) {
        if (is_array($primary_key)) {
            foreach ($primary_key as $key => $value) {
                $this->db->where($key, $value);
            }
        } else {
            $this->db->where($this->get_primary_key(), $primary_key);
        }
        return $this->_update($this->values);
    }

    public function insert($values) {
        return $this->_create($values);
    }

    public function index_rows($rows, $index_by) {
        $result_array = array();
        foreach ($rows as $row_index => $row_values) {
            $result_array[$row_values[$index_by]] = $row_values;
        }

        return $result_array;
    }

}