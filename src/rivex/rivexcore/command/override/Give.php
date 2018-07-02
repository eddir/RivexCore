<?php

namespace rivex\rivexcore\command\override;

use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use rivex\rivexcore\Main;

class Give extends OverrideCommand
{
    /**
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        parent::__construct($main, "give", "Give some items", "[player] [item] [count]");
        $this->setPermission("rivex.command.give.use");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $alias, array $args)
    {
        if (count($args) < 1) {
            $this->sendUsage($sender, $alias);
            return false;
        }
        $player = $sender->getServer()->getPlayer($args[0]);
        if ($player === null) {
            $itemname = $args[0];
            if ($sender instanceof Player) {
                $player = $sender;
                var_dump("tut");
                var_dump($sender->hasPermission('rivex.command.give.use'));
                if (!$sender->hasPermission('rivex.command.give.use')) {
                    $sender->sendMessage('§cВы не имеете право выдавать себе!');
                    return false;
                }
            } else {
                $sender->sendMessage('§cУкажите ник игрока!');
                return false;
            }
        } else {
            $itemname = $args[1];
            array_shift($args);
            if ($sender->getName() == $player->getName()) {
                if (!$sender->hasPermission('rivex.command.give.use')) {
                    $sender->sendMessage('§cУ вас нет прав выдавать себе!');
                    return false;
                }
            } else if (!$sender->hasPermission('rivex.command.give.other')) {
                $sender->sendMessage('§cВы не имеете право выдавать другим!');
                return false;
            }
        }
        try {
            $item = ItemFactory::fromString($itemname);
        } catch (\InvalidArgumentException $e) {
            $sender->sendMessage('§cНе найден предмет или игрок под названием ' . $itemname);
            return true;
        }
        if ($this->isBlocked($item) && !$sender->hasPermission('rivex.command.give.blocked')) {
            $sender->sendMessage('§cЭтот предмет запрещено выдавать!');
            return false;
        }
        if (!isset($args[1])) {
            $item->setCount($item->getMaxStackSize());
        } else {
            $item->setCount((int)$args[1]);
        }
        if (isset($args[2])) {
            $tags = $exception = null;
            $data = implode(" ", array_slice($args, 2));
            try {
                $tags = JsonNbtParser::parseJSON($data);
            } catch (\Throwable $ex) {
                $exception = $ex;
            }
            if (!($tags instanceof CompoundTag) or $exception !== null) {
                $sender->sendMessage('§cВы ввели неизвестный тег');
                return true;
            }
            $item->setNamedTag($tags);
        }
        //TODO: overflow
        $player->getInventory()->addItem(clone $item);
        $sender->sendMessage('§eИгроку ' . $player->getName() . ' выдано ' . $item->getCount() . ' ' . $item->getName());
        $player->sendMessage('§eВам выдано ' . $item->getCount() . ' ' . $item->getName());
        return true;
    }

    public function isBlocked(Item $item)
    {
        if ($item->getId() == Block::WOOD || $item->getId() == Block::COBBLESTONE) {
            return true;
        }
        return false;
    }
} 
