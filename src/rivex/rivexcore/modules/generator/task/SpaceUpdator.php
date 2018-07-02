<?php

namespace rivex\rivexcore\modules\generator\task;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

use rivex\rivexcore\modules\generator\space\SpaceGenerator;

/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 15.06.18
 * Time: 22:36
 */
class SpaceUpdator extends Task
{

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            /** @var Player $player */
            switch (SpaceGenerator::getLocationAt($player->getFloorX(), $player->getFloorZ(), $player->getLevel())[0]) {
               case 'Earth':
                   $effect = new EffectInstance(Effect::getEffect(Effect::SPEED), 1500, 1);
                   $player->addEffect($effect);
                   break;
               case 'Sun':
                   $player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP), 1500, 1));
                   //$player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 1500, 0.5));
                   break;
               default:
                   $player->removeAllEffects();
                   break;
           }
        }
    }
}
