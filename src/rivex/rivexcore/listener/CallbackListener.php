<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/3/18
 * Time: 8:34 PM
 */

namespace rivex\rivexcore\listener;


use pocketmine\event\entity\EntityDamageByEntityEvent;

use pocketmine\event\Listener;
use rivex\rivexcore\Main;

class CallbackListener implements Listener
{

    private $callbacks = array(
        "EntityDamageByEntityEvent" => array()
    );

    private $main;

    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    public function callback(string $event, callable $function, ...$args)
    {
        $this->callbacks[$event][] = array($function, $args);
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onDamage(EntityDamageByEntityEvent $event)
    {
        foreach ($this->callbacks["EntityDamageByEntityEvent"] as $key => $callback) {
            if ($callback[0]($event, $callback[1])) {
                unset($this->callbacks["EntityDamageByEntityEvent"][$key]);
            }
        }
    }

    /**
     * @return Main
     */
    public function getMain(): Main
    {
        return $this->main;
    }

}