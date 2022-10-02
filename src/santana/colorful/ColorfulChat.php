<?php

declare(strict_types=1);

namespace santana\colorful;

use cooldogedev\libSQL\ConnectionPool;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use santana\colorful\command\ColorfulChatCommand;
use santana\colorful\constant\DatabaseConstants;
use santana\colorful\query\TableInitialisationQuery;
use santana\colorful\session\SessionManager;

final class ColorfulChat extends PluginBase
{
    /**
     * @var ConnectionPool
     */
    protected ConnectionPool $connectionPool;

    /**
     * @var SessionManager
     */
    protected SessionManager $sessionManager;

    public const PREFIX = "§r§l§aColor§dChat §r§7» §r§f";

    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager
    {
        return $this->sessionManager;
    }

    /**
     * @param SessionManager $sessionManager
     * @return void
     */
    public function setSessionManager(SessionManager $sessionManager): void
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * @return ConnectionPool
     */
    public function getConnectionPool(): ConnectionPool
    {
        return $this->connectionPool;
    }

    /**
     * @param ConnectionPool $connectionPool
     * @return void
     */
    public function setConnectionPool(ConnectionPool $connectionPool): void
    {
        $this->connectionPool = $connectionPool;
    }

    /**
     * @return void
     */
    protected function onEnable(): void
    {
        if (!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);

        $this->sessionManager = new SessionManager($this);

        $this->connectionPool = new ConnectionPool($this, [
            "provider" => "sqlite",
            "threads" => DatabaseConstants::THREADS_COUNT,
            "sqlite" => [
                "file" => DatabaseConstants::DATABASE_NAME
            ],
        ]);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("colorfulchat", new ColorfulChatCommand($this));

        $this->connectionPool->submit(new TableInitialisationQuery());
    }
}