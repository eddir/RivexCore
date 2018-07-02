<?php
declare(strict_types=1);

namespace rivex\rivexcore\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use rivex\rivexcore\Main;

abstract class RivexCommand extends Command implements PluginIdentifiableCommand
{
    /** @var Main */
    private $main;
    /** @var bool|string */
    private $consoleUsageMessage;

    /**
     * @param Main $main
     * @param string $name
     * @param string $description
     * @param string $usageMessage
     * @param bool|null|string $consoleUsageMessage
     * @param array $aliases
     */
    public function __construct(Main $main, string $name, string $description = "", string $usageMessage = "", $consoleUsageMessage = true, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->main = $main;
        $this->consoleUsageMessage = $consoleUsageMessage;
    }

    /**
     * @return Main
     */
    public final function getPlugin(): Plugin
    {
        return $this->getMain();
    }

    /**
     * @return Main
     */
    public final function getMain(): Main
    {
        return $this->main;
    }

    /**
     * @return string
     */
    public function getUsage(): string
    {
        return "/" . parent::getName() . " " . parent::getUsage();
    }

    /**
     * @return bool|null|string
     */
    public function getConsoleUsage()
    {
        return $this->consoleUsageMessage;
    }

    /**
     * Function to give different type of usages, switching from "Console" and "Player" executors of a command.
     * This function can be overridden to fit any command needs...
     *
     * @param CommandSender $sender
     * @param string $alias
     */
    public function sendUsage(CommandSender $sender, string $alias): void
    {
        $message = TextFormat::RED . "Usage: " . TextFormat::GRAY . "/$alias ";
        if (!$sender instanceof Player) {
            if (is_string($this->consoleUsageMessage)) {
                $message .= $this->consoleUsageMessage;
            } elseif (!$this->consoleUsageMessage) {
                $message = TextFormat::RED . "[Error] Please run this command in-game";
            } else {
                $message .= str_replace("[player]", "[player]", parent::getUsage());
            }
        } else {
            $message .= parent::getUsage();
        }
        $sender->sendMessage($message);
    }
}
