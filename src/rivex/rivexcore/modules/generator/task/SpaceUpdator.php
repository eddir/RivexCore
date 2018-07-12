<?php

namespace rivex\rivexcore\modules\generator\task;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

use rivex\rivexcore\modules\generator\space\location\Earth;
use rivex\rivexcore\modules\generator\space\location\Mercury;
use rivex\rivexcore\modules\generator\space\location\Neptune;
use rivex\rivexcore\modules\generator\space\location\Sun;
use rivex\rivexcore\modules\generator\space\location\Uranus;
use rivex\rivexcore\modules\generator\space\SpaceGenerator;
use rivex\rivexcore\modules\generator\space\structure\SugarCane;

/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 15.06.18
 * Time: 22:36
 */
class SpaceUpdator extends Task
{

    private $first = true;

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        if ($this->first) {
            $level = Server::getInstance()->getDefaultLevel();
            $level->stopTime();
            $level->setTime(Level::TIME_NIGHT);
            $this->first = false;
        }
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            /** @var Player $player */
            $planet = SpaceGenerator::getLocationAt($player->getFloorX(), $player->getFloorZ(), $player->getLevel());
            if ($planet instanceof Neptune || $planet instanceof Uranus || $planet instanceof Mercury) {
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP), 1500, 1));
            } else {
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 1500, 1));
            }
            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 1500, 1));
        }
    }
}
