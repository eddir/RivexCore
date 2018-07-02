<?php

namespace rivex\rivexcore\modules\fraction\task;

/**
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

use pocketmine\scheduler\Task;
use rivex\rivexcore\modules\fraction\FractionManager;

class GeneratorTask extends Task
{
    /** @var FractionManager $module */
    private $module;
    private $step = false;

    public function __construct(FractionManager $module)
    {
        $this->module = $module;
    }

    /**
     * @return FractionManager
     */
    public function getModule()
    {
        return $this->module;
    }

    public function onRun($ticks)
    {
        if ($this->step) {
            $this->getModule()->getMain()->getDbLocal()->query('UPDATE `fractions` SET `generator_wood` = `generator_wood` + 1 WHERE `generator_alive` = 1');
        }
        $this->step = !$this->step;
        $this->getModule()->getMain()->getDbLocal()->query('UPDATE `fractions` SET `generator_cobblestone` = `generator_cobblestone` + 1 WHERE `generator_alive` = 1');
    }
}
