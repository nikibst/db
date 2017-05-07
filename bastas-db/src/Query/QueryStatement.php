<?php

namespace Bastas\Db\Query;

use Bastas\Db\PDOStatementGeneral;

/**
 * Class QueryStatement
 * @package Bastas\Db\Query
 */
class QueryStatement extends PDOStatementGeneral
{
    /**
     * QueryStatement constructor.
     * @param \PDOStatement $pdoStmt
     * @param string $driverName
     * @param string $sqlQuery
     * @param array $args
     * @param int|string $paramsType
     */
    public function __construct(
        \PDOStatement $pdoStmt,
        $driverName,
        $sqlQuery,
        array $args = [],
        $paramsType = PDOStatementGeneral::NAMED_PARAMS
    ) {
        parent::__construct($pdoStmt, $driverName, $sqlQuery, $args, $paramsType);
    }

    /**
     *
     */
    public function fetch()
    {
        /**
         * @TODO Implement the body of this method
         */
    }

    /**
     * @param int $colNumber
     * @return string
     */
    public function fetchColumn(int $colNumber = 0) : string
    {
        $this->execute();

        if ($colNumber > 0) {
            return $this->getPdoStatement()->fetchColumn($colNumber);
        }

        return $this->getPdoStatement()->fetchColumn();
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function fetchObject(string $className)
    {
        $this->execute();
        return $this->getPdoStatement()->fetchObject($className);
    }

    /**
     * @param string $fetchStyle
     * @param string $fetchArg
     * @return array
     */
    public function fetchAll(string $fetchStyle = '', string $fetchArg = '')
    {
        $this->execute();

        if ('' !== $fetchStyle) {
            if ('' !== $fetchArg) {
                return $this->getPdoStatement()->fetchAll($fetchStyle, $fetchArg);
            }
            return $this->getPdoStatement()->fetchAll($fetchStyle);
        }
        return $this->getPdoStatement()->fetchAll();
    }

    /**
     * @return bool
     */
    public function executeQuery()
    {
        return $this->execute();
    }
}
