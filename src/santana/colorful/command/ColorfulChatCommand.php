<?php

declare(strict_types=1);

namespace santana\colorful\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use santana\colorful\ColorfulChat;
use santana\colorful\permission\Permissions;

final class ColorfulChatCommand extends Command
{
    /**
     * @param ColorfulChat $plugin
     */
    public function __construct(protected ColorfulChat $plugin)
    {
        parent::__construct("colorchat", "Personalize your Chat Content Color.", null, ["cc"]);
        $this->setPermission(Permissions::COLORFULCHAT_COMMAND);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }
        
        $session = $this->plugin->getSessionManager()->get($sender);
        $session->sendMenu();
    }
}