<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 6/22/18
 * Time: 11:09 PM
 */

namespace rivex\rivexcore\command\override;


use pocketmine\command\CommandSender;
use pocketmine\Player;
use rivex\rivexcore\Main;

class Help extends OverrideCommand
{

    public function __construct(Main $main)
    {
        parent::__construct($main, "help", "Get some information about commands", "/help",
            "help", ["h", "?"]);
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
            $this->getMain()->getWindows()->getByName("help")->show($sender);
        } else {
            $sender->sendMessage("Команда доступна в игре");
        }
        return true;
    }
}
