<?php

namespace components;

/**
 * Class DBwrapper
 */
class DBwrapper
{
    /**
     * @var \PDO
     */
    private $db;

    /**
     * @var bool
     */
    public $lastResult = false;

    public $fetchStyle = \PDO::FETCH_ASSOC;

    /**
     * @param $config array
     */
    public function init(array $config)
    {
        try {
            $this->db = new \PDO($config['dsn'], $config['user'], $config['password']);
        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return \PDOStatement
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $pdoStatement = $this->db->prepare($sql);
        $this->lastResult = $pdoStatement->execute($params);

        return $pdoStatement;
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return mixed
     */
    public function findOne(string $sql, array $params = [])
    {
        return $this->query($sql, $params)->fetch($this->fetchStyle);
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return array
     */
    public function findAll(string $sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchAll($this->fetchStyle);
    }

    /**
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}
