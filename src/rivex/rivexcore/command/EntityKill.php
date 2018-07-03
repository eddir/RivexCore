<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/3/18
 * Time: 8:47 PM
 */

namespace rivex\rivexcore\command;

use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

use rivex\rivexcore\Main;

class EntityKill extends RivexCommand
{

    public function __construct(Main $main)
    {
        parent::__construct($main, "entitykill", "Kill the entity by damage them",
            "/entitykill", "entitykill", ["ekill"]);
        $this->setPermission("rivex.command.ekill");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $this->getMain()->getEvents()->callback("EntityDamageByEntityEvent",
                function (EntityDamageByEntityEvent $event, array $args) {
                    $damager = $event->getDamager();
                    /** @var Player $player */
                    $player = $args[0];
                    if ($damager instanceof Player and $damager->getName() == $player->getName()) {
                        $event->getEntity()->kill();
                        return true;
                    }
                    return false;
                }, $sender
            );
            $sender->sendMessage("§aУдарьте моба для его исчезновения.");
        } else {
            if (isset($args[0])) {
                $entity = $this->getMain()->getServer()->getDefaultLevel()->getEntity($args[0]);
                if ($entity !== null) {
                    $entity->kill();
                    $sender->sendMessage("Моб убит");
                } else {
                    $sender->sendMessage("Не найден моб с таким id");
                }
            } else {
                $sender->sendMessage("Необходимо указывать id");
            }
        }
        return true;
    }
}