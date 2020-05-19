<?php

namespace components;

/**
 * Class Cache
 */
class Cache
{
    protected const MAX_ROWS = 100 * 100;

    /**
     * @var DBwrapper
     */
    protected $db;

    /**
     * @var \Memcached
     */
    protected $cache;

    /**
     * Cache constructor.
     *
     * @param DBwrapper $db
     * @param string $host
     * @param int $port
     */
    public function __construct(DBwrapper $db, string $host, int $port)
    {
        $this->db = clone $db;
        $this->cache = new \Memcached();
        $this->cache->addServer($host, $port);
    }

    /**
     * @param string $table
     * @param array $conditions
     *
     * @return array
     */
    public function getPkByConditions(string $table, array $conditions): array
    {
        $sort = $conditions['sort'] ?? '';
        $offset = $conditions['offset'] ?? 0;
        $limit = $conditions['limit'] ?? 0;

        if (is_array($sort) && false === empty($sort)) {
            $sort = implode(', ', $sort);
        }

        return $this->getPk($table, $sort, $limit, $offset);
    }

    /**
     * @param string $table
     * @param string $sort
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    protected function getPk(string $table, string $sort, int $limit, int $offset): array
    {
        $cachePrefix = md5($table . $sort);
        $keys = $this->prepareKeys($cachePrefix, $limit, $offset);

        if (null === $ids = $this->getMulti($keys)) {
            $sql = 'SELECT id FROM ' . $table
                . ( $sort !== '' ? ' ORDER BY ' . $sort : '')
                . ' LIMIT ' . self::MAX_ROWS;
            $this->db->fetchStyle = \PDO::FETCH_COLUMN;
            $r = $this->db->findAll($sql);
            /*
             * @todo May be need to log
             */
            $this->store($cachePrefix, $r);

            $ids = array_slice($r, $offset, $limit);
        }

        return $ids;
    }

    /**
     * @param string $prefix
     * @param array $ids
     *
     * @return bool
     */
    protected function store(string $prefix, array $ids): bool
    {
        $r = [];

        foreach ($ids as $i => $v) {
            $r[$prefix . '_' . $i] = $v;
        }

        return $this->cache->setMulti($r, 60 * 60);
    }

    /**
     * @param string $prefix
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    protected function prepareKeys(string $prefix, int $limit, int $offset): array
    {
        $r = [];
        
        for ($i = 0; $i < $limit; $i++) {
            $r[] = $prefix . '_' . ($offset + $i);
        }
        
        return $r;
    }

    /**
     * @param array $keys
     *
     * @return array|null
     */
    protected function getMulti(array $keys): ?array
    {
        $r = $this->cache->getMulti($keys);

        return $r ? array_values($r) : null;
    }
}
