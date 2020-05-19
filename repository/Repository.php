<?php

namespace repository;

use components\DBwrapper;
use model\Model;

/**
 * Class Repository
 */
abstract class Repository
{
    protected const MODEL_CLASS = null;

    /**
     * @var DBwrapper
     */
    protected $db;

    public function __construct(DBwrapper $db)
    {
        if (null === static::MODEL_CLASS) {
            throw new \RuntimeException('Constant MODEL_CLASS should be set');
        }

        $this->db = $db;
    }

    /**
     * @param array $conditions
     * @param array $params
     *
     * @return Model|null
     */
    public function find(array $conditions = [], array $params = []): ?Model
    {
        $class = static::MODEL_CLASS;
        /** @var Model $model */
        $model = new $class($this->db);
        $sql = "SELECT * FROM " . $model->getTableName();

        if (!empty($conditions) && is_array($conditions)) {
            $where = implode(' AND ', $conditions);
            $sql .= " WHERE " . $where;
        }

        $queryResult = $this->db->findOne($sql, $params);

        if (is_array($queryResult)) {
            $model->setAttributes($queryResult);

            return $model;
        }

        return null;
    }

    /**
     * @param array $conditions
     * @param array $params
     * @param bool $asArray
     *
     * @return Model[]
     */
    public function findAll(array $conditions = [], array $params = [], bool $asArray = false): array
    {
        $class = static::MODEL_CLASS;
        /** @var Model $model */
        $model = new $class($this->db);
        $select = $conditions['select'] ?? '*';
        $where = $conditions['where'] ?? [];
        $sort = $conditions['sort'] ?? [];
        $limit = $conditions['limit'] ?? null;
        $offset = $conditions['offset'] ?? null;

        if (is_array($select)) {
            $select = implode(', ', $select);
        }

        $sql = 'SELECT ' . $select . ' FROM ' . $model->getTableName();

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

        if (!empty($_q) && false === $asArray) {
            $class = static::MODEL_CLASS;

            foreach ($_q as $i => $attr) {
                /** @var $_m Model */
                $_m = new $class($this->db);
                $_q[$i] = $_m->setAttributes($attr);
            }
        }

        return $_q;
    }

    /**
     * @param int $pk
     *
     * @return Model|null
     */
    public function findByPk(int $pk): ?Model
    {
        $class = static::MODEL_CLASS;
        /** @var Model $model */
        $model = new $class($this->db);
        $conditions = [$model->getPk() . ' = ' . $pk];

        return $this->find($conditions);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $class = static::MODEL_CLASS;
        /** @var Model $model */
        $model = new $class($this->db);
        $sql = 'DELETE FROM '
            . $model->getTableName()
            . ' WHERE ' . $model->getPk() . ' = :pk';
        $this->db->query($sql, [':pk' => $model->getAttribute($model->getPk())]);

        return $this->db->lastResult;
    }

    /**
     * @param Model $model
     *
     * @return bool
     */
    public function save(Model $model): bool
    {
        if (null !== $model->getAttribute($model->getPk())) {
            return $this->insert($model);
        }

        return $this->update($model);
    }


    /**
     * @param Model $model
     *
     * @return bool
     */
    public function insert(Model $model): bool
    {
        $sql = 'INSERT INTO ' . $model->getTableName();
        $fields = $model->getFields();
        $pkI = array_search($model->getPk(), $fields, true);
        array_splice($fields, $pkI, 1);
        $params = [];

        foreach ($fields as $i => $name) {
            $_v = $model->getAttribute($name);

            if (!isset($_v) || empty($_v)) {
                unset($fields[$i]);
            } else {
                $params[':' . $name] = $_v;
            }
        }

        $sql .= ' (' . implode(' , ', $fields) . ') VALUES (' . implode(',', array_keys($params)) . ')';
        $this->db->query($sql, $params);

        if ($this->db->lastResult === true) {
            $model->setPk($this->db->lastInsertId());
        }

        return $this->db->lastResult;
    }

    /**
     * @param Model $model
     *
     * @return bool
     */
    public function update(Model $model): bool
    {
        $sql = "UPDATE " . $model->getTableName() . " SET ";
        $fields = $model->getFields();
        $pkI = array_search($model->getPk(), $fields, true);
        array_splice($fields, $pkI, 1);
        $params = [];
        $ins = [];

        foreach ($fields as $i => $name) {
            $_v = $model->getAttribute($name);

            if (!isset($_v) || empty($_v)) {
                unset($fields[$i]);
            } else {
                $params[':' . $name] = $_v;
                $ins[] = $name . " = :" . $name;
            }
        }

        $sql .= implode(' , ', $ins) . ' WHERE ' . $model->getPk() . ' = :pk';
        $params[':pk'] = $model->getAttribute($model->getPk());
        $this->db->query($sql, $params);

        return $this->db->lastResult;
    }
}
