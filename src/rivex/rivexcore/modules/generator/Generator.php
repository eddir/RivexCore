<?php

namespace rivex\rivexcore\modules\generator;

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
 * January 2018
 */
use pocketmine\level\generator\GeneratorManager;

use rivex\rivexcore\Main;
use rivex\rivexcore\modules\generator\task\SpaceUpdator;
use rivex\rivexcore\modules\generator\space\SpaceGenerator;

class Generator
{

    const ENABLE = true;
    private $main;

    private static $generators = array(
        SpaceGenerator::NAME => SpaceGenerator::class
    );

    public function __construct(Main $main)
    {
        $this->main = $main;
        // TODO: настройка включения генератора
        if (self::ENABLE) {
            GeneratorManager::addGenerator($this->getGenerator("space"), "space");
            $this->getMain()->getScheduler()->scheduleDelayedRepeatingTask(new SpaceUpdator(), 20 * 60, 20 * 30);
        }
    }

    /**
     * @param string $name
     * @return null|string
     */
    public static function getGenerator(string $name): ?string
    {
        return self::$generators[$name] ?? null;
    }

    public function getMain()
    {
        return $this->main;
    }

}
