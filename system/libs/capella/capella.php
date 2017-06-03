<?php
class Capella extends PDO
{
    private $sql;
    private $tableName;
    private $where;
    private $join;
    private $orderBy;
    private $groupBy;
    private $limit;
    private $page;
    private $totalRecord;
    private $pageCount;
    private $paginationLimit;
    private $html;
    private $op;
    public function __construct($o)
    {
        try {
            $o['charset'] = (isset($o['charset'])) ? $o['charset'] : 'utf8';
            $o['collation'] = (isset($o['collation'])) ? $o['collation'] : 'utf8_general_ci';
            parent::__construct('mysql:host=' . $o['host'] . ';dbname=' . $o['name'], $o['username'], $o['password']);
            $this->query('SET CHARACTER SET ' . $o['charset']);
            $this->query('SET NAMES ' . $o['collation']);
            $this->op = true;
        }
        catch (PDOException $e)
        {
            $this->op = false;
        }
    }
    public function from($tableName)
    {
        $this->sql = 'SELECT * FROM `' . $tableName . '`';
        $this->tableName = $tableName;
        return $this;
    }
    public function select($from)
    {
        $this->sql = str_replace('*', $from, $this->sql);
        return $this;
    }
    public function where($column, $value = '', $mark = '=', $logical = '&&')
    {
        $this->where[] = [$column, $value,$mark,$logical];
        return $this;
    }
    public function or_where($column, $value, $mark = '=')
    {
        $this->where($column, $value, $mark, '||');
        return $this;
    }
    public function join($targetTable, $joinSql, $joinType = 'inner')
    {
        $this->join[] = ' ' . strtoupper($joinType) . ' JOIN ' . $targetTable . ' ON ' . sprintf($joinSql, $targetTable, $this->tableName);
        return $this;
    }
    public function orderby($columnName, $sort = 'ASC')
    {
        $this->orderBy = ' ORDER BY ' . $columnName . ' ' . strtoupper($sort);
        return $this;
    }
    public function groupby($columnName)
    {
        $this->groupBy = ' GROUP BY ' . $columnName;
        return $this;
    }
    public function limit($start, $limit)
    {
        $this->limit = ' LIMIT ' . $start . ',' . $limit;
        return $this;
    }
    public function run()
    {
        $query = $this->generateQuery();
        return $query->fetchAll(parent::FETCH_ASSOC);
    }
    public function first()
    {
        $query = $this->generateQuery();
        return $query->fetch(parent::FETCH_ASSOC);
    }
    public function generateQuery()
    {
        if ($this->join) {
            $this->sql .= implode(' ', $this->join);
            $this->join = null;
        }
        $this->get_where();
        if ($this->groupBy) {
            $this->sql .= $this->groupBy;
            $this->groupBy = null;
        }
        if ($this->orderBy) {
            $this->sql .= $this->orderBy;
            $this->orderBy = null;
        }
        if ($this->limit) {
            $this->sql .= $this->limit;
            $this->limit = null;
        }
        $query = $this->query($this->sql);
        return $query;
    }
    private function get_where()
    {
        if (is_array($this->where) && count($this->where) > 0) {
            $this->sql .= ' WHERE ';
            $where = [];
            foreach ($this->where as $key => $arg) {
                if ($arg[2] == 'LIKE' || $arg[2] == 'NOT LIKE') {
                    $where[] = $arg[3] . ' ' . $arg[0] . ' ' . $arg[2] . ' "%' . $arg[1] . '%" ';
                } elseif ($arg[2] == 'BETWEEN' || $arg[2] == 'NOT BETWEEN') {
                    $where[] = $arg[3] . ' ' . ($arg[0] . ' ' . $arg[2] . ' ' . $arg[1][0] . ' AND ' . $arg[1][1]);
                } elseif ($arg[2] == 'FIND_IN_SET') {
                    $where[] = $arg[3] . ' FIND_IN_SET("' . (is_array($arg[1]) ? implode(',', $arg[1]) : $arg[1]) . '", ' . $arg[0] . ')';
                } elseif ($arg[2] == 'IN' || $arg[2] == 'NOT IN') {
                    $where[] = $arg[3] . ' ' . $arg[0] . ' ' . $arg[2] . '(' . (is_array($arg[1]) ? implode(',', $arg[1]) : $arg[1]) . ')';
                } else {
                    $where[] = $arg[3] . ' ' . $arg[0] . ' ' . $arg[2] . ' "' . $arg[1] . '"';
                }
            }
            $this->sql .= ltrim(implode(' ', $where), '&&');
            $this->where = null;
        }
    }
    public function insert($tableName)
    {
        $this->sql = 'INSERT INTO ' . $tableName;
        return $this;
    }
    public function set($columns)
    {
        $val = [];
        $col = [];
        foreach ($columns as $column => $value) {
            $val[] = $value;
            $col[] = $column . ' = ? ';
        }
        $this->sql .= ' SET ' . implode(', ', $col);
        $this->get_where();
        $query = $this->prepare($this->sql);
        $result = $query->execute($val);
        return $result;
    }
    public function lastId()
    {
        return $this->lastInsertId();
    }
    public function update($columnName)
    {
        $this->sql = 'UPDATE ' . $columnName;
        return $this;
    }
    public function delete($columnName)
    {
        $this->sql = 'DELETE FROM ' . $columnName;
        return $this;
    }
    public function done()
    {
        $this->get_where();
        $query = $this->exec($this->sql);
        return $query;
    }
    public function total()
    {
        if ($this->join) {
            $this->sql .= implode(' ', $this->join);
            $this->join = null;
        }
        $this->get_where();
        if ($this->orderBy) {
            $this->sql .= $this->orderBy;
            $this->orderBy = null;
        }
        if ($this->groupBy) {
            $this->sql .= $this->groupBy;
            $this->groupBy = null;
        }
        if ($this->limit) {
            $this->sql .= $this->limit;
            $this->limit = null;
        }
        $query = $this->query($this->sql)->fetch(parent::FETCH_ASSOC);
        return $query['total'];
    }
    public function pagination($totalRecord, $paginationLimit, $pageParamName)
    {
        $this->paginationLimit = $paginationLimit;
        $this->page = isset($_GET[$pageParamName]) && is_numeric($_GET[$pageParamName]) ? $_GET[$pageParamName] : 1;
        $this->totalRecord = $totalRecord;
        $this->pageCount = ceil($this->totalRecord / $this->paginationLimit);
        $start = ($this->page * $this->paginationLimit) - $this->paginationLimit;
        return [
            'start' => $start,
            'limit' => $this->paginationLimit
        ];
    }
    public function showPagination($url, $class = 'active')
    {
        if ($this->totalRecord > $this->paginationLimit) {
            for ($i = $this->page - 5; $i < $this->page + 5 + 1; $i++) {
                if ($i > 0 && $i <= $this->pageCount) {
                    $this->html .= '<li class="';
                    $this->html .= ($i == $this->page ? $class : null);
                    $this->html .= '"><a href="' . str_replace('[page]', $i, $url) . '">' . $i . '</a>';
                }
            }
            return $this->html;
        }
    }
    public function nextPage()
    {
        return ($this->page + 1 < $this->pageCount ? $this->page + 1 : $this->pageCount);
    }
    public function prevPage()
    {
        return ($this->page - 1 > 0 ? $this->page - 1 : 1);
    }
    public function getSqlString()
    {
        return $this->sql;
    }
    public function between($column, $values = [])
    {
        $this->where($column, $values, 'BETWEEN');
        return $this;
    }
    public function not_between($column, $values = [])
    {
        $this->where($column, $values, 'NOT BETWEEN');
        return $this;
    }
    public function find_in_set($column, $value)
    {
        $this->where($column, $value, 'FIND_IN_SET');
        return $this;
    }
    public function in($column, $value)
    {
        $this->where($column, $value, 'IN');
        return $this;
    }
    public function not_in($column, $value)
    {
        $this->where($column, $value, 'NOT IN');
        return $this;
    }
    public function like($column, $value)
    {
        $this->where($column, $value, 'LIKE');
        return $this;
    }
    public function not_like($column, $value)
    {
        $this->where($column, $value, 'NOT LIKE');
        return $this;
    }
}
