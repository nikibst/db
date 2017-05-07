<?php

namespace Bastas\Db;

use Bastas\Db\Exception\QueryInfoException;

/**
 * Class QueryInfo
 * @package Bastas\Db
 */
final class QueryInfo
{
    /**
     * @var
     */
    private $pdoInjectedInstance;
    /**
     * @var
     */
    private $driverName;

    /**
     * QueryInfo constructor.
     * @param $pdoInjectedInstance
     * @param $driverName
     */
    public function __construct($pdoInjectedInstance, $driverName)
    {
        $this->pdoInjectedInstance = $pdoInjectedInstance;
        $this->driverName = $driverName;
    }

    /**
     * @return string
     */
    public function getErrorCode() : string
    {
        return $this->pdoInjectedInstance->errorCode();
    }

    /**
     * @return array
     */
    protected function getErrorInfo() : array
    {
        return $this->pdoInjectedInstance->errorInfo();
    }

    /**
     * @return string
     * @throws QueryInfoException
     */
    public function getSqlStateErrorMessage() : string
    {
        $errorInfo = $this->getErrorInfo();

        if (empty($errorInfo)) {
            throw new QueryInfoException("Sql Error has not been occured.");
        }

        if (!isset($errorInfo[0])) {
            throw new QueryInfoException($this->driverName . " Driver did not returned any SQLSTATE error message");
        }

        return $errorInfo[0];
    }

    /**
     * @return string
     * @throws QueryInfoException
     */
    public function getDriverSpecificErrorCode() : string
    {
        $errorInfo = $this->getErrorInfo();

        if (empty($errorInfo)) {
            throw new QueryInfoException("Sql Error has not been occured.");
        }

        if (!isset($errorInfo[1])) {
            throw new QueryInfoException($this->driverName . " Driver did not returned any driver specific error code");
        }

        return $errorInfo[1];
    }

    /**
     * @return string
     * @throws QueryInfoException
     */
    public function getDriverSpecificErrorMessage() : string
    {
        $errorInfo = $this->getErrorInfo();

        if (empty($errorInfo)) {
            throw new QueryInfoException("Sql Error has not been occured.");
        }

        if (!isset($errorInfo[2])) {
            throw new QueryInfoException(
                $this->driverName . " Driver did not returned any driver specific error message"
            );
        }

        return $errorInfo[2];
    }

    /**
     * @return int
     * @throws QueryInfoException
     */
    public function getRowCountAffected() : int
    {
        if (!$this->pdoInjectedInstance instanceof \PDOStatement) {
            throw new QueryInfoException("No PDOStatement has been executed yet");
        }

        return $this->pdoInjectedInstance->rowCount();
    }

    /**
     * @return int
     * @throws QueryInfoException
     */
    public function getColumnCount() : int
    {
        if (!$this->pdoInjectedInstance instanceof \PDOStatement) {
            throw new QueryInfoException("No PDOStatement has been executed yet");
        }

        return $this->pdoInjectedInstance->columnCount();
    }

    /**
     * @param int $column
     * @return array
     * @throws QueryInfoException
     */
    public function getColumnMeta(int $column) : array
    {
        if (!$this->pdoInjectedInstance instanceof \PDOStatement) {
            throw new QueryInfoException("No PDOStatement has been executed yet");
        }

        if ($this->driverName !== PDOAdapter::PDO_DBLIB ||
            $this->driverName !== PDOAdapter::PDO_MYSQL ||
            $this->driverName !== PDOAdapter::PDO_PGSQL ||
            $this->driverName !== PDOAdapter::PDO_SQLITE
        ) {
            throw new QueryInfoException("This PDO method is not currently supported for driver " . $this->driverName);
        }

        return $this->pdoInjectedInstance->getColumnMeta($column);
    }

    /**
     * @return bool
     * @throws QueryInfoException
     */
    public function dumpDebugInfo()
    {
        if (!$this->pdoInjectedInstance instanceof \PDOStatement) {
            throw new QueryInfoException("No PDOStatement has been executed yet");
        }

        return $this->pdoInjectedInstance->debugDumpParams();
    }
}
