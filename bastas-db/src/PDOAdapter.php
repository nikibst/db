<?php

namespace Bastas\Db;

use Bastas\Db\Command\CommandStatement;
use Bastas\Db\Exception\PDOAdapterException;
use Bastas\Db\Query\QueryStatement;

final class PDOAdapter extends AbstractDbAdapter implements QueryInfoInterface
{
    const PDO_CUBRID = 'cubrid';
    const PDO_MSSQL = 'mssql';
    const PDO_SYBASE = 'sybase';
    const PDO_DBLIB = 'dblib';
    const PDO_FIREBIRD = 'firebird';
    const PDO_IBM = 'ibm';
    const PDO_INFORMIX = 'informix';
    const PDO_MYSQL = 'mysql';
    const PDO_SQLSRV = 'sqlsrv';
    const PDO_OCI = 'oci';
    const PDO_ODBC = 'odbc';
    const PDO_PGSQL = 'pgsql';
    const PDO_SQLITE = 'sqlite';
    const PDO_SQLITE2 = 'sqlite2';
    const PDO_4D = '4d';

    public function __construct(
        string $dsn,
        string $username = '',
        string $password = '',
        string $defaultCharset = 'utf8',
        string $attrErrorMode = \PDO::ERRMODE_SILENT,
        bool $attrEmulatePrepares = true,
        string $attrColumnCase = \PDO::CASE_NATURAL,
        int $attrTimeout = 30,
        bool $attrAutocommit = true,
        string $attrDefaultFetchMode = \PDO::FETCH_BOTH
    ) {
        preg_match('/^(.*?):/', $dsn, $this->driverName);

        if (null === $this->driverName || !isset($this->driverName[1]) || $this->driverName[1] === "") {
            throw new PDOAdapterException("Driver not provided in DSN");
        }

        $this->driverName = strtolower($this->driverName[1]);

        $this->retrievePdoInstance($dsn, $username, $password);
        $this->assignPdoAttributes(
            $attrErrorMode,
            $attrEmulatePrepares,
            $attrColumnCase,
            $attrTimeout,
            $attrAutocommit,
            $attrDefaultFetchMode);

        $this->adapter->exec("SET sql_mode='NO_BACKSLASH_ESCAPES'");
        $this->adapter->exec("SET NAMES " . $defaultCharset);
    }

    private function retrievePdoInstance(string $dsn, string $username, string $password)
    {
        switch ($this->driverName) {
            case 'mysql':
                $this->adapter = new \PDO($dsn, $username, $password);
                break;
            case PDOAdapter::PDO_CUBRID: case PDOAdapter::PDO_MSSQL:case PDOAdapter::PDO_SYBASE:case PDOAdapter::PDO_DBLIB:
            case PDOAdapter::PDO_FIREBIRD:case PDOAdapter::PDO_IBM:case PDOAdapter::PDO_INFORMIX:case PDOAdapter::PDO_SQLSRV:
            case PDOAdapter::PDO_OCI:case PDOAdapter::PDO_ODBC:case PDOAdapter::PDO_PGSQL:case PDOAdapter::PDO_SQLITE:
            case PDOAdapter::PDO_SQLITE2:case PDOAdapter::PDO_4D:
                throw new PDOAdapterException("Driver " . $this->driverName . " is not supported");
            default:
                throw new PDOAdapterException("Driver " . $this->driverName . " does not exist");
        }
    }

    private function assignPdoAttributes(
        string $attrErrorMode,
        bool $attrEmulatePrepares,
        string $attrColumnCase,
        int $attrTimeout,
        bool $attrAutocommit,
        string $attrDefaultFetchMode
    ) {
        $this->adapter->setAttribute(\PDO::ATTR_ERRMODE, $attrErrorMode);
        $this->adapter->setAttribute(\PDO::ATTR_EMULATE_PREPARES, $attrEmulatePrepares);
        $this->adapter->setAttribute(\PDO::ATTR_CASE, $attrColumnCase);
        $this->adapter->setAttribute(\PDO::ATTR_TIMEOUT, $attrTimeout);
        $this->adapter->setAttribute(\PDO::ATTR_AUTOCOMMIT, $attrAutocommit);
        $this->adapter->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $attrDefaultFetchMode);
    }

    public function openSqlTransaction()
    {
        return $this->adapter->beginTransaction();
    }

    public function commitSqlTransaction()
    {
        return $this->adapter->commit();
    }

    public function rollbackSqlTransaction()
    {
        return $this->adapter->rollBack();
    }

    public function isInSqlTransaction() : bool
    {
        return $this->adapter->inTransaction();
    }

    public function prepareSqlQueryStatement(
        string $sqlQuery,
        array $argsToBind = [],
        int $paramsType = 1
    ) : QueryStatement {

        $pdoStmt = $this->adapter->prepare($sqlQuery);
        return new QueryStatement($pdoStmt, $this->driverName, $sqlQuery, $argsToBind, $paramsType);
    }

    public function prepareSqlCommandStatement(
        string $sqlQuery,
        array $argsToBind = [],
        int $paramsType = 1
    ) : CommandStatement {

        $pdoStmt = $this->adapter->prepare($sqlQuery);
        return new CommandStatement($pdoStmt, $this->driverName, $sqlQuery, $argsToBind, $paramsType);
    }

    public function getLastInsertedId(string $name = '') : string
    {
        return $this->adapter->lastInsertId($name);
    }

    public function getQueryInfo(string $driverName)
    {
        return new QueryInfo($this->adapter, $driverName);
    }
}
