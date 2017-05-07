<?php

namespace Bastas\Db\Command;

use Bastas\Db\PDOStatementGeneral;
use Bastas\Db\QueryInfo;

/**
 * Class CommandStatement
 * @package Bastas\Db\Command
 */
class CommandStatement extends PDOStatementGeneral
{
    /**
     * CommandStatement constructor.
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
     * @return int
     */
    public function executeCommand()
    {
        $this->execute();
        $queryInfo = new QueryInfo($this->getPdoStatement(), $this->driverName);

        return $queryInfo->getRowCountAffected();
    }
}
