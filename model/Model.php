<?php

namespace model;

use components\DBwrapper;

/**
 * @package model
 */
class Model
{
    /** @var DBwrapper */
    private $db;

    private $pk = 'id';

    private $errors = [];

    protected $fields = [];

    protected $tableName = '';

    /**
     * @var string[]
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @param $db DBwrapper
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Model
     */
    public function addError(string $name, string $value): self
    {
        $this->errors[$name] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getPk(): string
    {
        return $this->pk;
    }

    /**
     * @param string $pk
     *
     * @return Model
     */
    public function setPk(string $pk): self
    {
        $this->pk = $pk;

        return $this;
    }

    /**
     * @return DBwrapper
     */
    public function getDb(): DBwrapper
    {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function getAttribute($name): string
    {
        return $this->attributes[$name] ?? "";
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Model
     */
    public function setAttribute(string $name, string $value = ''): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param array $attr
     *
     * @return Model
     */
    public function setAttributes(array $attr): self
    {
        $this->attributes = $attr;

        return $this;
    }

    /**
     * @param array $conditions
     * @param array $params
     *
     * @return bool
     */
    public function find(array $conditions = [], array $params = []): bool
    {
        $sql = "SELECT * FROM " . $this->getTableName();

        if (!empty($conditions) && is_array($conditions)) {
            $where = implode(' AND ', $conditions);
            $sql .= " WHERE " . $where;
        }

        $this->attributes = $this->db->findOne($sql, $params);

        return is_array($this->attributes);
    }

    /**
     * @param array $conditions
     * @param array $params
     *
     * @return array
     */
    public function findAll(array $conditions = [], array $params = []): array
    {
        $sql = "SELECT * FROM " . $this->getTableName();

        $where = $conditions['where'] ?? [];
        $sort = $conditions['sort'] ?? [];
        $limit = $conditions['limit'] ?? null;
        $offset = $conditions['offset'] ?? null;

        if (is_array($where) && false === empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        } elseif (false === empty($where)) {
            $sql .= ' WHERE ' . $where;
        }

        if (is_array($sort) && false === empty($sort)) {
            $sql .= ' ORDER BY ' . implode(', ', $sort);
        } elseif (false === empty($sort)) {
            $sql .= ' ORDER BY ' . $sort;
        }

        if (null !== $limit) {
            $sql .= ' LIMIT ' . $limit;
        }

        if (null !== $offset) {
            $sql .= ' OFFSET ' . $offset;
        }

        $_q = $this->db->findAll($sql, $params);

        if (!empty($_q)) {
            $className = static::class;

            foreach ($_q as $i => $attr) {
                /** @var $_m Model */
                $_m = new $className($this->db);
                $_m->attributes = $attr;
                $_q[$i] = $_m;
            }
        }

        return $_q;
    }

    /**
     * @param int $pk
     *
     * @return bool
     */
    public function findByPk(int $pk): bool
    {
        $conditions = [$this->getPk() . ' = ' . $pk];

        return $this->find($conditions);
    }

    /**
     * @return bool
     */
    public function insert(): bool
    {
        $sql = "INSERT INTO " . $this->getTableName();
        $pkI = array_search($this->pk, $this->fields, true);
        $fields = $this->fields;
        array_splice($fields, $pkI, 1);
        $params = [];

        foreach ($fields as $i => $name) {
            $_v = $this->getAttribute($name);

            if (!isset($_v) || empty($_v)) {
                unset($fields[$i]);
            } else {
                $params[':' . $name] = $_v;
            }
        }

        $sql .= " (" . implode(' , ', $fields) . ") VALUES (" . implode(',', array_keys($params)) . ")";
        $this->db->query($sql, $params);

        if ($this->db->lastResult === true) {
            $this->attributes[$this->pk] = $this->db->lastInsertId();
        }

        return $this->db->lastResult;
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        $sql = "UPDATE " . $this->getTableName() . " SET ";
        $pkI = array_search($this->pk, $this->fields, true);
        $fields = $this->fields;
        array_splice($fields, $pkI, 1);
        $params = [];
        $ins = [];

        foreach ($fields as $i => $name) {
            $_v = $this->getAttribute($name);

            if (!isset($_v) || empty($_v)) {
                unset($fields[$i]);
            } else {
                $params[':' . $name] = $_v;
                $ins[] = $name . " = :" . $name;
            }
        }

        $sql .= implode(" , ", $ins) . " WHERE " . $this->pk . " = " . $this->attributes[$this->pk];
        $this->db->query($sql, $params);

        return $this->db->lastResult;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!isset($this->attributes[$this->pk])) {
            return $this->insert();
        }

        return $this->update();
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $sql = "DELETE FROM " . $this->getTableName() . " WHERE " . $this->pk . " = " . $this->getAttribute($this->pk);
        $this->db->query($sql);

        return $this->db->lastResult;
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public function load(array $data = []): self
    {
        foreach ($data as $name => $value) {
            if (in_array($name, $this->fields, true)) {
                $this->attributes[$name] = trim($value);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $this->errors = [];
        foreach ($this->rules as $attr => $rules) {
            foreach ($rules as $rule) {
                $err = null;

                switch ($rule['type']) {
                    case 'required':
                        $err = validateRequire($this->attributes[$attr], $rule);
                        break;
                    case 'regExp':
                        $err = validateRegExp($this->attributes[$attr], $rule);
                        break;
                    case 'length':
                        $err = validate($this->attributes[$attr], $rule);
                        break;
                    case 'file':
                        $err = validateFile($this->attributes[$attr], $rule);
                        break;
                    default:
                }

                if (!is_null($err)) {
                    $this->errors[$attr] = $err;
                    break;
                }
            }
        }

        return empty($this->errors);
    }
}
