<?php

namespace Bastas\Db;

use Bastas\Db\Exception\PDOStatementGeneralException;

abstract class PDOStatementGeneral implements QueryInfoInterface
{
    const NAMED_PARAMS = 1;
    const SEQUENTIAL_PARAMS = 2;

    private $pdoStmt;
    protected $driverName;

    public function __construct(
        \PDOStatement $pdoStmt,
        string $driverName,
        string $sqlQuery,
        array $args = [],
        string $paramsType = PDOStatementGeneral::NAMED_PARAMS
    ) {
        $this->pdoStmt = $pdoStmt;
        $this->driverName = $driverName;

        if (!empty($args)) {
            $this->bindParams($sqlQuery, $args, $paramsType);
        }
    }

    private function bindParams(string $query, array $args, int $paramsType)
    {
        switch ($paramsType) {
            case PDOStatementGeneral::NAMED_PARAMS:
                $this->bindNamedParams($query, $args);
                break;
            case PDOStatementGeneral::SEQUENTIAL_PARAMS:
                $this->bindSequentialParams($args);
                break;
            default:
                throw new PDOStatementGeneralException("Param type " . $paramsType . " is wrong");
        }
    }

    private function bindNamedParams(string $query, array $args)
    {
        preg_match_all('/:[a-zA-Z0-9]*/', $query, $queryParams);
        $queryParams = $queryParams[0];
        $queryParamsCount = count($queryParams);

        for ($i = 0; $i < $queryParamsCount; ++$i) {
            if (is_array($args[$i])) {
                if (!isset($args[$i]['value'])) {
                    throw new PDOStatementGeneralException("Key with name 'value' is required.");
                }

                if (!isset($args[$i]['type'])) {
                    throw new PDOStatementGeneralException("Key with name 'type' is required.");
                }
                $this->pdoStmt->bindParam($queryParams[$i], $args[$i]['value'], $args[$i]['type']);
            } else {
                $this->pdoStmt->bindParam($queryParams[$i], $args[$i]);
            }
        }
    }

    private function bindSequentialParams(array $args)
    {
        $argsCount = count($args);

        for ($i = 0; $i < $argsCount; ++$i) {
            if (is_array($args[$i])) {
                if (!isset($args[$i]['value'])) {
                    throw new PDOStatementGeneralException("Key with name 'value' is required.");
                }

                if (!isset($args[$i]['type'])) {
                    throw new PDOStatementGeneralException("Key with name 'type' is required.");
                }
                $this->pdoStmt->bindParam($i + 1, $args[$i]['value'], $args[$i]['type']);
            } else {
                $this->pdoStmt->bindParam($i + 1, $args[$i]);
            }
        }
    }

    public function closeCursor() : bool
    {
        return $this->pdoStmt->closeCursor();
    }

    protected function execute()
    {
        $stmtExecution = $this->pdoStmt->execute();

        if (false === $stmtExecution) {
            throw new PDOStatementGeneralException("Statement execution was a failure.");
        }

        return $stmtExecution;
    }

    public function getPdoStatement()
    {
        return $this->pdoStmt;
    }

    public function getQueryInfo(string $driverName)
    {
        return new QueryInfo($this->getPdoStatement(), $driverName);
    }
}
