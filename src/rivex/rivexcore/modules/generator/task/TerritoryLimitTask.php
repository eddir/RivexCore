<?php

namespace rivex\rivexcore\modules\generator\task;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\math\Vector2;


/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 15.06.18
 * Time: 22:36
 */
class TerritoryLimitTask extends Task
{
	
	private $center;
	
	private $radius;
	
	public function __construct(Vector2 $center, int $radius)
	{
		$this->center = $center;
		$this->radius = $radius;
	}

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $p) {
            /** @var Player $player */
            if ($this->center->distance($p->x, $p->z) > $this->radius) {
				$p->teleport($p->getLevel()->getSpawnLocation());
				$p->sendMessage('Вы не имеете право посещать эти неуютные места.');
			}
        }
    }
}
