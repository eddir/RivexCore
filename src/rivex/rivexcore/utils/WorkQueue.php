<?php

namespace rivex\rivexcore\utils;

/**
 *  ╔╗──╔══╗╔═════╗╔╗╔════╗╔══╗──╔╗
 *  ║║──║╔═╝║╔════╝╚╝║╔══╗║║╔╗╚╗─║║
 *  ║║──║╚═╗║║╔═══╗╔╗║║──║║║║╚╗╚╗║║
 *  ║║──║╔═╝║║╚══╗║║║║║──║║║║─╚╗╚╝║
 *  ║╚═╗║╚═╗║╚═══╝║║║║╚══╝║║║──╚╗─║
 *  ╚══╝╚══╝╚═════╝╚╝╚════╝╚╝───╚═╝
 *
 *              Ядро LegionCore. Написано
 *      эксклюзивно для LegionDygers.
 *      Право использования читайте в
 *      приложении LICENSE. Правообла-
 *      датели в приложении README.md
 *
 * @author Eduard Rostkov
 * @link eddirworkmail@gmail.com
 * @link http://vk.com/mcpelove_mdygers
 * @copyright Eddir 2015 - 2016
 */

use pocketmine\scheduler\Task;
use rivex\rivexcore\Main;

class WorkQueue extends Task
{

    private $enable = true;

    private $works = array();

    private $interval = 5;

    private $current = 0;

    private $main;

    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    public function init()
    {
        $this->getMain()->getScheduler()->scheduleRepeatingTask($this, 20);
    }

    public function addWork($callback, $time, $args = [])
    {
        $this->works[] = array('callback' => $callback, 'time' => $this->current + $time, 'args' => $args);
    }

    public function onRun($currentTicks)
    {
        $this->current++;
        foreach ($this->works as $key => $work) {
            if ($work['time'] <= $this->current) {
                call_user_func_array($work['callback'], $work['args']);
                unset($this->works[$key]);
            }
        }
    }

    public function getMain()
    {
        return $this->main;
    }
}
