<?php

namespace rivex\rivexcore\modules\report;

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
use pocketmine\entity\Entity;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\fraction\entity\Hunkey;
use rivex\rivexcore\modules\fraction\task\GeneratorTask;

class ReportManager
{

    private $main;

    public const INDEPENDENT = 0;
    public const LEADER = 1;
    public const DEPUTY = 2;
    public const MEMBER = 3;

    public function __construct(Main $main)
    {
        $this->main = $main;
        $this->getMain()->getDbLocal()->createTable('fractions', [
            'id' => 'INT(6) NOT NULL AUTO_INCREMENT',
            'name' => 'VARCHAR(32) NOT NULL',
            'description' => 'VARCHAR(32) NOT NULL',
            'generator_need_wood' => 'INT(6) NOT NULL DEFAULT 2000',
            'generator_need_cobblestone' => 'INT(6) NOT NULL DEFAULT 2000',
            'generator_wood' => 'INT NOT NULL DEFAULT 0',
            'generator_cobblestone' => 'INT NOT NULL DEFAULT 0',
            'generator_alive' => 'BOOLEAN NOT NULL DEFAULT 0',
            'generator_id' => 'INT(9) NULL DEFAULT NULL'
        ]);
        $this->getMain()->getDbLocal()->createTable('users', [
            'id' => 'INT(6) NOT NULL AUTO_INCREMENT',
            'name' => 'VARCHAR(32) NOT NULL',
            'kills' => 'INT NOT NULL DEFAULT 0',
            'deaths' => 'INT NOT NULL DEFAULT 0',
            'fraction' => 'VARCHAR(32) NULL DEFAULT NULL',
            'rank' => 'INT NOT NULL DEFAULT 0'
        ]);
        $this->getMain()->getDbLocal()->createTable('invites', [
            'id' => 'INT(6) NOT NULL AUTO_INCREMENT',
            'user' => 'VARCHAR(32) NOT NULL',
            'fraction' => 'VARCHAR(32) NOT NULL'
        ]);
        $this->getMain()->getScheduler()->scheduleDelayedRepeatingTask(new GeneratorTask($this), 20 * 60, 20 * 60 * 10);
        if (!is_dir($this->getMain()->getDataFolder() . 'skins/')) {
            mkdir($this->getMain()->getDataFolder() . 'skins/');
            stream_copy_to_stream($resource = $this->getMain()->getResource("skins/Hunkey.bin"), $fp = fopen($this->getMain()->getDataFolder() . "/skins/Hunkey.bin", "wb"));
            fclose($fp);
            fclose($resource);
        }
        Entity::registerEntity(Hunkey::class, true);
    }

    public function getMain()
    {
        return $this->main;
    }
}