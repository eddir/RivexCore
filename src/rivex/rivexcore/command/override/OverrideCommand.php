<?php

namespace rivex\rivexcore\command\override;

use rivex\rivexcore\command\RivexCommand;
use rivex\rivexcore\Main;

abstract class OverrideCommand extends RivexCommand
{
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
        parent::__construct($main, $name, $description, $usageMessage, $consoleUsageMessage, $aliases);
        $commandMap = $main->getServer()->getCommandMap();
        $command = $commandMap->getCommand($name);
        $command->setLabel($name . "_disabled");
        $command->unregister($commandMap);
    }
}
