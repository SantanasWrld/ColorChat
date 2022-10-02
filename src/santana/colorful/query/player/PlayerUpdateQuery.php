<?php

declare(strict_types=1);

namespace santana\colorful\query\player;

use cooldogedev\libSQL\query\SQLiteQuery;
use santana\colorful\constant\DatabaseConstants;
use SQLite3;

final class PlayerUpdateQuery extends SQLiteQuery
{
    /**
     * @param string $xuid
     * @param string $data
     */
    public function __construct(protected string $xuid, protected string $data)
    {
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param SQLite3 $connection
     * @return void
     */
    public function onRun(SQLite3 $connection): void
    {
        $data = json_decode($this->data, true);

        $statement = $connection->prepare($this->getQuery());
        $statement->bindValue(":xuid", $this->xuid);
        $statement->bindValue(":color", $data["color"]);

        $statement->execute()?->finalize();
        $statement->close();

        $this->setResult($connection->changes() > 0);
    }

    /**
     * @return string
     */
    protected function getQuery(): string
    {
        return "UPDATE " . DatabaseConstants::TABLE_COLORFUL_CHAT . " SET color = :color WHERE xuid = :xuid";
    }

    /**
     * @return string
     */
    public function getXuid(): string
    {
        return $this->xuid;
    }

    /**
     * @param string $xuid
     * @return void
     */
    public function setXuid(string $xuid): void
    {
        $this->xuid = $xuid;
    }

    /**
     * @param string $data
     * @return void
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }
}