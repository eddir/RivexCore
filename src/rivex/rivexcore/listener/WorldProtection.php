<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 6/27/18
 * Time: 11:13 PM
 */

namespace rivex\rivexcore\listener;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\Player;
use rivex\rivexcore\Main;

class WorldProtection implements Listener
{
    /** @var Main $main */
    private $main;

    /**
     * WorldProtection constructor.
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    public function getMain()
    {
        return $this->main;
    }

    public function onBreak(BlockBreakEvent $event)
    {
        if (!$event->getPlayer()->isOp()) {
            $event->setCancelled();
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        if (!$event->getPlayer()->isOp()) {
            $event->setCancelled();
        }
    }

    public function onDecay(LeavesDecayEvent $event)
    {
        $event->setCancelled();
    }

    public function onSignChange(SignChangeEvent $event)
    {
        if (!$event->getPlayer()->isOp()) {
            $event->setCancelled();
        }
    }

    public function onPacketReceive(DataPacketReceiveEvent $event)
    {
        $pk = $event->getPacket();
        if ($pk instanceof ItemFrameDropItemPacket) {
            $event->setCancelled();
        }
    }

    public function onBucketFill(PlayerBucketFillEvent $event)
    {
        if (!$event->getPlayer()->isOp()) {
            $event->setCancelled();
        }
    }

    public function onBucketEmpty(PlayerBucketEmptyEvent $event)
    {
        if (!$event->getPlayer()->isOp()) {
            $event->setCancelled();
        }
    }

    public function onDamage(EntityDamageEvent $event)
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            if ($event->getEntity() instanceof Player and $event->getDamager() instanceof Player) {
                $event->setCancelled();
            }
        } else {
            $event->setCancelled();
        }
    }

    public function onExplode(EntityExplodeEvent $event)
    {
        $event->setCancelled();
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        if (!$event->getPlayer()->isOp()) {
            $event->setCancelled();
        }
    }

    public function onBlockChange(EntityBlockChangeEvent $event)
    {
        $event->setCancelled();
    }

    public function onHunger(PlayerExhaustEvent $event)
    {
        $event->setCancelled();
    }

}
