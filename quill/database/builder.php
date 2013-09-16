<?php namespace Quill\Database;

abstract class Builder {
    public function wrap_columns($columns) {
        $wrapped = array();
        foreach($columns as $column) {
            $wrapped[] = $this->wrap_column($column);
        }
        return implode(', ', $wrapped);
    }
    public function wrap_column($value) {
        $params = array();
        if(strpos($value, '(')) return $value;
        if(strpos(strtolower($value), ' as ')) {
            $parts = explode(' ', $value);
            return $this->wrap_column($parts[0]) . ' AS ' . $this->wrap_column($parts[2]);
        }
        $parts = explode('.', $value);
        foreach($parts as $index => $string) {
            if($string != '*') {
                if($index == 0 and count($parts) > 1) {
                    $string = $this->wrap_table($string);
                }
                else {
                    $string = $this->wrap_value($string);
                }
            }
            $params[] = $string;
        }
        return implode('.', $params);
    }
    public function wrap_table($value) {
        if($this->connection->table_prefix) {
            if(strpos($value, $this->connection->table_prefix) === 0) {
                return $this->wrap_value($value);
            }
        }
        return $this->wrap_value($this->connection->table_prefix . $value);
    }
    public function wrap_value($value) {
        return sprintf($this->connection->wrapper, $value);
    }
    public function placeholders($length, $holder = '?') {
        $holders = array();
        for($i = 0; $i < $length; $i++) {
            $holders[] = $holder;
        }
        return implode(', ', $holders);
    }
    public function build() {
        $sql = '';
        if(count($this->join)) {
            $sql .= ' ' . implode(' ', $this->join);
        }
        if(count($this->where)) {
            $sql .= ' ' . implode(' ', $this->where);
        }
        if(count($this->groupby)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupby);
        }
        if(count($this->sortby)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->sortby);
        }
        if($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
            if($this->offset) {
                $sql .= ' OFFSET ' . $this->offset;
            }
        }
        return $sql;
    }
    public function build_insert($row) {
        $keys = array_keys($row);
        $values = $this->placeholders(count($row));
        $this->bind = array_values($row);
        return 'INSERT INTO ' . $this->wrap_table($this->table) . ' (' . $this->wrap_columns($keys) . ') VALUES(' . $values . ')';
    }
    public function build_update($row) {
        $placeholders = array();
        $values = array();
        foreach($row as $key => $value) {
            $placeholders[] = $this->wrap_column($key) . ' = ?';
            $values[] = $value;
        }
        $update = implode(', ', $placeholders);
        $this->bind = array_merge($values, $this->bind);
        return 'UPDATE ' . $this->wrap_table($this->table) . ' SET ' . $update . $this->build();
    }
    public function build_select($columns = null) {
        if(is_array($columns) and count($columns)) {
            $columns = $this->wrap_columns($columns);
        }
        else $columns = '*';
        return 'SELECT ' . $columns . ' FROM ' . $this->wrap_table($this->table) . $this->build();
    }
    public function build_delete() {
        return 'DELETE FROM ' . $this->wrap_table($this->table) . $this->build();
    }
    public function build_select_count() {
        return 'SELECT COUNT(*) FROM ' . $this->wrap_table($this->table) . $this->build();
    }
}