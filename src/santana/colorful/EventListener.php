<?php

declare(strict_types=1);

namespace santana\colorful;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use santana\colorful\constant\ColorConstants;
use santana\colorful\query\player\PlayerUpdateQuery;

final class EventListener implements Listener
{
    /**
     * @param ColorfulChat $plugin
     */
    public function __construct(protected ColorfulChat $plugin)
    {
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->plugin->getSessionManager()->get($player);

        if ($session->isUpdated()) {
            $this->plugin->getConnectionPool()->submit(new PlayerUpdateQuery($player->getXuid(), $session->getData()));
        }

        $this->plugin->getSessionManager()->remove($player);
    }

    /**
     * @param PlayerLoginEvent $event
     * @return void
     */
    public function onLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        $this->plugin->getSessionManager()->add($player);
    }

    /**
     * @param PlayerChatEvent $event
     * @return void
     */
    public function onChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->plugin->getSessionManager()->get($player);

        $format = match ($session->getColor()) {
            ColorConstants::RED => TextFormat::RED,
            ColorConstants::DARK_RED => TextFormat::DARK_RED,
            ColorConstants::AQUA => TextFormat::AQUA,
            ColorConstants::GRAY => TextFormat::GRAY,
            ColorConstants::GOLD => TextFormat::GOLD,
            ColorConstants::GREEN => TextFormat::GREEN,
            ColorConstants::PURPLE => TextFormat::DARK_PURPLE,
            ColorConstants::DARK_GREEN => TextFormat::DARK_GREEN,
            ColorConstants::PINK => TextFormat::LIGHT_PURPLE,
            ColorConstants::DARK_BLUE => TextFormat::DARK_BLUE,
            ColorConstants::BLUE => TextFormat::BLUE,
            ColorConstants::WHITE => TextFormat::WHITE,
            ColorConstants::UNKNOWN => TextFormat::BLACK
        };

        if ($session->getColor() == ColorConstants::UNKNOWN) {
            $event->setFormat($event->getFormat());
        } else {
            Server::getInstance()->broadcastMessage(Server::getInstance()->getLanguage()->translateString($event->getFormat(), [$event->getPlayer()->getDisplayName(), $format . $event->getMessage()]), $event->getRecipients());
            $event->cancel();
        }
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