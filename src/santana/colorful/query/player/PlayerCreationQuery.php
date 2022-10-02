<?php

declare(strict_types=1);

namespace santana\colorful\query\player;

use santana\colorful\constant\DatabaseConstants;
use cooldogedev\libSQL\query\SQLiteQuery;
use SQLite3;

final class PlayerCreationQuery extends SQLiteQuery
{
    /**
     * @param string $xuid
     */
    public function __construct(protected string $xuid)
    {
    }

    /**
     * @param SQLite3 $connection
     * @return void
     */
    public function onRun(SQLite3 $connection): void
    {
        $statement = $connection->prepare($this->getQuery());
        $statement->bindValue(":xuid", $this->xuid);

        $statement->execute();
        $this->setResult($connection->changes() > 0);
        $statement->close();
    }

    /**
     * @return string
     */
    public function getXuid(): string
    {
        return $this->xuid;
    }

    /**
     * @return string
     */
    protected function getQuery(): string
    {
        return "INSERT OR IGNORE INTO " . DatabaseConstants::TABLE_COLORFUL_CHAT . " (xuid) VALUES (:xuid)";
    }

    /**
     * @param string $xuid
     * @return void
     */
    public function setXuid(string $xuid): void
    {
        $this->xuid = $xuid;
    }
}