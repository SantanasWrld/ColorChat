<?php

declare(strict_types=1);

namespace santana\colorful\session;

use cooldogedev\libSQL\context\ClosureContext;
use Exception;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use santana\colorful\ColorfulChat;
use santana\colorful\constant\ColorConstants;
use santana\colorful\permission\Permissions;
use santana\colorful\query\player\PlayerCreationQuery;
use santana\colorful\query\player\PlayerRetrieveQuery;

final class Session
{
    /**
     * @var bool
     */
    protected bool $updated = false;

    /**
     * @var string|null
     */
    protected ?string $color = null;

    /**
     * @param ColorfulChat $plugin
     * @param Player $player
     */
    public function __construct(protected ColorfulChat $plugin, protected Player $player)
    {
        $this->plugin->getConnectionPool()->submit(new PlayerRetrieveQuery($this->player->getXuid()), context: ClosureContext::create(
            function (?array $data): void {
                if ($data !== null) {
                    $this->color = $data["color"] ?? ColorConstants::UNKNOWN;
                } else {
                    $this->plugin->getConnectionPool()->submit(new PlayerCreationQuery($this->player->getXuid()));
                }
            }
        ));
    }

    /**
     * @return void
     */
    public function sendMenu(): void
    {
        if (!$this->player->isConnected()) {
            return;
        }

        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName("§r§l§aColor §dSelector");

        $menu->getInventory()->setContents($this->getLayout());

        $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($menu): void {
            $item = $transaction->getItemClicked();

            if (!$item->isNull()) {
                try {
                    $type = $item->getNamedTag()->getString(ColorConstants::COLOR_IDENTIFIER);
                } catch (Exception) {
                    return;
                }

                $color = match ($type) {
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

                $this->toggle($type);
                $this->player->sendMessage(ColorfulChat::PREFIX . "§r§fSuccessfully Selected the Color §l$color" . ucfirst($type) . "§r§f.");
                $menu->onClose($this->player);
            }
        }));

        $menu->send($this->player);
    }

    /**
     * @param bool $displayLore
     * @return array
     */
    public function getLayout(bool $displayLore = true): array
    {
        $inv = [
            "inventory" => [
                "layout" => [
                    "red" => [
                        "custom-name" => "&r&l&cRed",
                        "item" => "minecraft:wool",
                        "meta" => 14,
                        "slot" => 10
                    ],
                    "dark_red" => [
                        "custom-name" => "&r&l&4Dark Red",
                        "item" => "minecraft:stained_hardened_clay",
                        "meta" => 14,
                        "slot" => 12
                    ],
                    "aqua" => [
                        "custom-name" => "&r&l&bAqua",
                        "item" => "minecraft:wool",
                        "meta" => 3,
                        "slot" => 14
                    ],
                    "gray" => [
                        "custom-name" => "&r&l&7Gray",
                        "item" => "minecraft:wool",
                        "meta" => 7,
                        "slot" => 16
                    ],
                    "gold" => [
                        "custom-name" => "&r&l&6Gold",
                        "item" => "minecraft:wool",
                        "meta" => 1,
                        "slot" => 20
                    ],
                    "green" => [
                        "custom-name" => "&r&l&aGreen",
                        "item" => "minecraft:wool",
                        "meta" => 5,
                        "slot" => 22
                    ],
                    "purple" => [
                        "custom-name" => "&r&l&5Purple",
                        "item" => "minecraft:wool",
                        "meta" => 2,
                        "slot" => 24
                    ],
                    "dark_green" => [
                        "custom-name" => "&r&l&2Dark Green",
                        "item" => "minecraft:wool",
                        "meta" => 13,
                        "slot" => 30
                    ],
                    "pink" => [
                        "custom-name" => "&r&l&dPink",
                        "item" => "minecraft:wool",
                        "meta" => 6,
                        "slot" => 32
                    ],
                    "dark_blue" => [
                        "custom-name" => "&r&l&1Dark Blue",
                        "item" => "minecraft:wool",
                        "meta" => 11,
                        "slot" => 38
                    ],
                    "blue" => [
                        "custom-name" => "&r&l&9Blue",
                        "item" => "minecraft:wool",
                        "meta" => 9,
                        "slot" => 40
                    ],
                    "white" => [
                        "custom-name" => "&r&l&fWhite",
                        "item" => "minecraft:wool",
                        "meta" => 0,
                        "slot" => 42
                    ],
                ]
            ]
        ];

        $items = [];

        $color = match ($this->getColor()) {
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
            default => TextFormat::BLACK
        };

        $data = $inv["inventory"];

        $lore = [
            "§r$color * §r§7Clicking this Item will",
            "§r$color * §r§7Change the Color of your",
            "§r$color * §r§7Chat Messages . ",
            "§r§8Permission: " . Permissions::COLORFULCHAT_COMMAND
        ];

        foreach ($data["layout"] as $type => $itemData) {
            $item = StringToItemParser::getInstance()->parse($itemData["item"]);
            $i = ItemFactory::getInstance()->get($item->getId(), intval($itemData["meta"]));

            if ($i === null) {
                continue;
            }

            $i->setCustomName(TextFormat::colorize($itemData["custom-name"]));
            $i->getNamedTag()->setString(ColorConstants::COLOR_IDENTIFIER, $type);
            $displayLore && $i->setLore($lore);

            $items[4] = ItemFactory::getInstance()->get(ItemIds::PAPER)->setCustomName("§r§7Selected Color: §r§l$color" . ucfirst($this->getColor()) ?? "Unknown");

            $items[$itemData["slot"]] = $i;
        }
        return $items;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     * @return void
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function toggle(string $key): bool
    {
        if (!$this->updated) {
            $this->updated = true;
        }

        if (in_array($key, ["red", "dark_red", "aqua", "gray", "gold", "green", "purple", "dark_green", "pink", "dark_blue", "blue", "white", "unknown"])) {
            $this->setColor($key);
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isUpdated(): bool
    {
        return $this->updated;
    }

    /**
     * @param bool $updated
     */
    public function setUpdated(bool $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return json_encode([
            "color" => $this->color ?? ColorConstants::UNKNOWN,
        ]);
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
     */
    public function setPlugin(ColorfulChat $plugin): void
    {
        $this->plugin = $plugin;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }
}