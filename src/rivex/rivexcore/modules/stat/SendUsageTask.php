<?php

/*
 * RivexCore
 *
 * @owner   Rivex™
 * @link    http://rivex.online
 * @link    admin@rivex.online
 *
 * @author  Eduard Rostkov
 * @link    http://rostkov.pro
 * @link    eddirworkmail@gmail.com
 *
 * February 2019
 */

namespace rivex\rivexcore\modules\stat;

use pocketmine\scheduler\Task;
use pocketmine\utils\Utils;

use rivex\rivexcore\Main;

class SendUsageTask extends Task{

	private $main;

	public function __construct(Main $main)
	{
		$this->main = $main;
	}

	public function onRun(int $currentTick)
	{
		$server = $this->main->getServer();
		$port = $server->getPort();
		$online = count($server->getOnlinePlayers());
		$tick_usage = $server->getTickUsage() * 100;
		$tps = $server->getTicksPerSecond();
		$memory = round(Utils::getMemoryUsage(true)[1] / 1024 / 1024);

		$this->main->getDbGlobal()->query('INSERT INTO uptime (server, online, tick_usage, tps, memory) VALUES (#d, #d, #d, #d, #d)', $port, $online, $tick_usage, $tps, $memory);
	}

}
