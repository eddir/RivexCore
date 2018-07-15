<?php

namespace rivex\rivexcore\modules\test;

use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use rivex\rivexcore\Main;

class Test implements Listener
{

    private $main;
    private $test = "ok";

    public function __construct(Main $main)
    {
        $this->main = $main;
        //$main->getServer()->getPluginManager()->registerEvents($this, $main);
    }

    public function getMain()
    {
        return $this->main;
    }

    public function test()
    {
    }

    private function educateTest()
    {
        echo 'Start educate test', PHP_EOL;
        for ($x = 0; $x < 16; ++$x) {
            echo $x, PHP_EOL;
        }
        echo 'End educate test', PHP_EOL;
    }

    private function futureTest(LevelLoadEvent $event)
    {
        $maxX = 0;
        $maxZ = 0;
        $minX = 0;
        $minZ = 0;
        $level = $event->getLevel();
        $config = (new Config($this->getMain()->getServer()->getDataPath() . 'plugin_data/SexGuard/region.json', Config::JSON))->getAll();
        foreach ($config as $region) {
            if ($region['min']['x'] >> 4 < $minX) $minX = $region['min']['x'] >> 4;
            if ($region['min']['z'] >> 4 < $minZ) $minZ = $region['min']['z'] >> 4;
            if ($region['max']['x'] >> 4 > $maxX) $maxX = $region['max']['x'] >> 4;
            if ($region['max']['z'] >> 4 > $maxZ) $maxZ = $region['max']['z'] >> 4;
        }
        for ($x = $minX - 16; $x < $maxX + 16; $x++) {
            for ($z = $minZ - 16; $z < $maxZ + 16; $z++) {
                foreach ($config as $region) {
                    if (($region['min']['x'] >> 4) <= $x && ($region['max']['x'] >> 4) >= $x) {
                        if (($region['min']['z'] >> 4) <= $z && ($region['max']['z'] >> 4) >= $z) {
                            continue 2;
                        }
                    }
                }
                $level->loadChunk($x, $z);
                $level->getChunk($x, $z)->setGenerated(false);
            }
        }
        return true;
    }

}
