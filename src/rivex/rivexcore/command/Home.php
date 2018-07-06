<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/6/18
 * Time: 8:30 PM
 */

namespace rivex\rivexcore\command;


use pocketmine\command\CommandSender;
use pocketmine\Player;
use rivex\rivexcore\Main;

class Home extends RivexCommand
{

    public function __construct(Main $main)
    {
        parent::__construct($main, "home", "Manage home list", "/home",
            "home", ["homes", "sethome", "sethomes", "gethome", "hom"]);
        $this->setPermission("rivex.command.home");
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
            $this->getMain()->getWindows()->getByName('homes')->show($sender);
        } else {
            $sender->sendMessage('Команда зарезервирована');
        }
        return true;
    }
}