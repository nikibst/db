<?php

namespace Bastas\Db;

/**
 * Class AbstractDbAdapter
 * @package Bastas\Db
 */
abstract class AbstractDbAdapter
{
    /**
     * @var null
     */
    protected $adapter = null;
    /**
     * @var null
     */
    protected $driverName = null;
    /**
     * @var string
     */
    protected $username = '';
    /**
     * @var string
     */
    protected $password = '';
    /**
     * @var string
     */
    protected $dsn = '';

    public function getPdoAdapter(): \PDO
    {
        return $this->adapter;
    }
}
