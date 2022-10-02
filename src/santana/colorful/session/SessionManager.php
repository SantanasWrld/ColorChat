<?php

declare(strict_types=1);

namespace santana\colorful\session;

use pocketmine\player\Player;
use santana\colorful\ColorfulChat;

final class SessionManager
{
    /**
     * @var Session[]
     */
    protected array $sessions = [];

    /**
     * @param ColorfulChat $plugin
     */
    public function __construct(protected ColorfulChat $plugin)
    {
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function add(Player $player): bool
    {
        if ($this->exists($player)) {
            return false;
        }

        $this->sessions[$player->getUniqueId()->getBytes()] = new Session($this->plugin, $player);
        return true;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function exists(Player $player): bool
    {
        return isset($this->sessions[$player->getUniqueId()->getBytes()]);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function remove(Player $player): bool
    {
        if (!$this->exists($player)) {
            return false;
        }

        unset($this->sessions[$player->getUniqueId()->getBytes()]);
        return true;
    }

    /**
     * @param Player $player
     * @return Session|null
     */
    public function get(Player $player): ?Session
    {
        return $this->sessions[$player->getUniqueId()->getBytes()] ?? null;
    }

    /**
     * @return ColorfulChat
     */
    public function getPlugin(): ColorfulChat
    {
        return $this->plugin;
    }

    /**
     * @param ColorfulChat $plugin
     * @return void
     */
    public function setPlugin(ColorfulChat $plugin): void
    {
        $this->plugin = $plugin;
    }
}