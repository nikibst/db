<?php

namespace Bastas\Db;

/**
 * Interface QueryInfoInterface
 * @package Bastas\Db
 */
interface QueryInfoInterface
{
    /**
     * @param string $driverName
     * @return mixed
     */
    public function getQueryInfo(string $driverName);
}
