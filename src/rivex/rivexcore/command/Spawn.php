<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/3/18
 * Time: 10:16 PM
 */

namespace rivex\rivexcore\command;


use pocketmine\command\CommandSender;
use pocketmine\Player;
use rivex\rivexcore\Main;

class Spawn extends RivexCommand
{

    public function __construct(Main $main)
    {
        parent::__construct($main, "spawn", "Teleport to spawn location",
            "/spawn", "spawn", ["s"]);
        $this->setPermission("rivex.command.spawn");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $sender->teleport($sender->getLevel()->getSpawnLocation());
        } else {
            if (isset($args[0])) {
                $player = $sender->getServer()->getPlayer($args[0]);
                if ($player !== null) {
                    $player->teleport($player->getLevel()->getSpawnLocation());
                    $sender->sendMessage("Игрок перемещён на спавн");
                } else {
                    $sender->sendMessage("Не удалось обнаружить игрока с таким ником");
                }
            } else {
                $sender->sendMessage("Необходимо указывать ник");
            }
        }
    }
}