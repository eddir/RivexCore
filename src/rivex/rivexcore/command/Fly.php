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

class Fly extends RivexCommand
{

    public function __construct(Main $main)
    {
        parent::__construct($main, "fly", "Switch fly mode", "/fly", "fly");
        $this->setPermission("rivex.command.fly");
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
	if (!$this->testPermission($sender)) {
		return true;
	}
	if ($sender instanceof Player) {
		$sender->setAllowFlight(!$sender->getAllowFlight());
		$sender->sendMessage("§eРежим полёта переключён.");
	} else {
		$sender->sendMessage("Команда доступна в игре.");
	}
        return true;
    }
}
