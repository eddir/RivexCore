<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 6/22/18
 * Time: 11:07 PM
 */

namespace rivex\rivexcore\command;


use pocketmine\command\CommandSender;
use pocketmine\Player;
use rivex\rivexcore\Main;

class Menu extends RivexCommand
{

    public function __construct(Main $main)
    {
        parent::__construct($main, "menu", "Get some information about commands", "/menu",
            "menu", ["m", "menu"]);
        $this->setPermission("rivex.command.help");
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
            var_dump("готовим");
            $this->getMain()->getWindows()->getByName("help")->show($sender);
        } else {
            $sender->sendMessage("Команда доступна в игре");
        }
        return true;
    }
}