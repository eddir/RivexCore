<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/6/18
 * Time: 9:39 PM
 */

namespace rivex\rivexcore\command;


use pocketmine\command\CommandSender;
use pocketmine\Player;
use rivex\rivexcore\Main;

class DeleteHome extends RivexCommand
{

    public function __construct(Main $main)
    {
        parent::__construct($main, "deletehome", "Remove home position", "/deletehome",
            "deletehome", ["delhome", "dhome", "rhome", "dh", "rh"]);
        $this->setPermission("rivex.command.deletehome");
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
            $this->getMain()->getWindows()->getByName('deletehome')->show($sender);
        } else {
            $sender->sendMessage('Зарезервированная команда');
        }
        return true;
    }
}