<?php

declare(strict_types=1);

namespace santana\colorful\query;

use santana\colorful\constant\ColorConstants;
use santana\colorful\constant\DatabaseConstants;
use cooldogedev\libSQL\query\SQLiteQuery;
use SQLite3;

final class TableInitialisationQuery extends SQLiteQuery
{
    /**
     * @param SQLite3 $connection
     * @return void
     */
    public function onRun(SQLite3 $connection): void
    {
        $statement = $connection->prepare($this->getQuery());
        $statement->execute();
        $statement->close();
    }

    /**
     * @return string
     */
    protected function getQuery(): string
    {
        return "CREATE TABLE IF NOT EXISTS " . DatabaseConstants::TABLE_COLORFUL_CHAT . " (
            xuid VARCHAR (16) PRIMARY KEY UNIQUE NOT NULL,
            color TEXT DEFAULT " . ColorConstants::UNKNOWN . "
        )";
    }
}