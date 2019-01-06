<?php

namespace rivex\rivexcore\listener;

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

use pocketmine\block\Block;
use pocketmine\block\ItemFrame;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\inventory\PlayerCursorInventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\Item;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\sound\GhastSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

use rivex\rivexcore\Main;
use rivex\rivexcore\modules\fraction\entity\Hunkey;
use rivex\rivexcore\modules\fraction\FractionManager;
use rivex\rivexcore\User;

class EventListener implements Listener
{
    /** @var Main */
    private $main;

    /**
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        $this->main = $main;
    }
    /**
     * @param  $event
     */
    /*
    public function onRegister(PlayerRegisterEvent $event)
    {

         * $this->getMain()->getWindows()->getByName('help')->show($event->getPlayer());
         *
         * return true;*/
    /**
     * $form = new HelpForm($event->getPlayer());
     *
     * $form = $this->getMain()->getFormAPI()->createSimpleForm(function (Player $player, array $data) {
     * return true;
     * });
     * $form->setTitle("Добро пожаловать!\nЗдесь текст про то, какой у нас §eклассный §fсервер\n\nЭто окно можно открыть командой /help.");
     * $form->addButton('Тест', 1, 'http://rostkov.pro/img.png');
     * $form->addButton('Легенда');
     * $form->addButton('Правила');
     * $form->addButton('Команды');
     * $form->addButton('Помощь');
     * $form->sendToPlayer($event->getPlayer());
     * }
     * @param PlayerLoginEvent $event
     */
    /*
        public function onAuthenticate(PlayerAuthenticateEvent $event)
        {
            $answer = $this->getMain()->getDbGlobal()->fetch_one('SELECT `id` FROM `tickets` WHERE `isread` = 0 AND `admin` = 1 AND `parent` IN (SELECT `id` FROM `tickets` WHERE `user` = #s AND `admin` = 0) LIMIT 1', $event->getPlayer()->getLowerCaseName());
            if ($answer) {
                $this->getMain()->getWorkQueue()->addWork(
                    function ($username, $id) {
                        if (($user = $this->getMain()->getUser($username))) {
                            $user->sendAnswer($id);
                            $this->getMain()->getDbGlobal()->query('UPDATE `tickets` SET `isread` = 1 WHERE `id` = #d', $id);
                        }
                    },
                    5,
                    [$event->getPlayer()->getName(), $answer['id']]
                );
            }
            return true;
        }
    */
    public function onLogin(PlayerLoginEvent $event)
    {
        $this->getMain()->addUser($event->getPlayer());


        //file_put_contents('./skin.bin', $event->getPlayer()->getSkin()->getSkinData());
    }

    public function onQuit(PlayerQuitEvent $event)
    {
	$this->getMain()->removeUser($event->getPlayer());

	if ($this->getMain()->getConfig('alwaysOnSpawn', false)) {
		$event->getPlayer()->teleport($event->getPlayer()->getLevel()->getSpawnLocation());
	}
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $user = $this->getMain()->getUser($event->getPlayer()->getName());
        $user->addDeath();

        $victim = $event->getEntity();
        if ($victim instanceof Player) {
            if ($event->getEntity()->getLastDamageCause()->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
                /** @var EntityDamageByEntityEvent $cause */
                $cause = $event->getEntity()->getLastDamageCause();
                $killer = $cause->getDamager();
                if ($killer instanceof Player) {
                    $user = $this->getMain()->getUser($killer->getName());
                    $user->addKill();
                }
            }
        }

        return true;
    }

    public function onEntityDeath(EntityDeathEvent $event)
    {
        $entity = $event->getEntity();
		if ($entity instanceof Hunkey) {
            $items = [];
            $drops = $this->getMain()->getDbLocal()->fetch_one('SELECT `generator_wood` AS `wood`, `generator_cobblestone` AS `cobblestone` FROM `fractions` WHERE `generator_id` = #d', $entity->getUId());
            if ($drops['wood'] > 0) {
                $items[] = Item::get(Item::WOOD, 0, $drops['wood']);
            }
            if ($drops['cobblestone'] > 0) {
                $items[] = Item::get(Item::COBBLESTONE, 0, $drops['cobblestone']);
            }
            $event->setDrops($items);
            $this->getMain()->getFractions()->removeGenerator($entity->getFraction());
            $event->getEntity()->getLevel()->addSound(new GhastSound($event->getEntity()));
            foreach ($this->getMain()->getUsers() as $user) {
                /** @var $user User */
                if ($user->getFraction() == $entity->getFraction()) {
                    $user->getPlayer()->sendMessage('§eЖителя Вашей фракции убили!');
                }
            }
        }
    }

    public function onUse(PlayerInteractEvent $event)
    {
        if ($event->getItem()->getId() == Item::SPAWN_EGG && $event->getItem()->getDamage() == Entity::VILLAGER) {
            if (($user = $this->getMain()->getFractions()->getSession($event->getPlayer()->getName()))) {
                $event->setCancelled();
                $player = $event->getPlayer();
                $user = $this->getMain()->getUser($player->getName());

                foreach ($player->getLevel()->getEntities() as $entity) {
                    if ($entity instanceof Hunkey && $entity->getFraction() == $user->getFraction()) {
                        $entity->kill();
                    }
                }

                Main::getInstance()->getFractions()->spawnHunkey($event->getBlock()->getSide($event->getFace()), $user->getFraction(), $player->getYaw() - 180, $player->getPitch() - 45);
                $player->getInventory()->remove($event->getItem());
                $player->sendMessage('§eГотово! Тапните по жителю для открытия меню');
            }
        } elseif ($event->getBlock() instanceof ItemFrame && $event->getPlayer()->getGameMode() != Player::SURVIVAL) {
            $event->setCancelled();
        }
    }

    public function onDamage(EntityDamageEvent $event)
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                $user = $this->getMain()->getUser($damager->getName());
                $entity = $event->getEntity();
                if ($entity instanceof Player) {
                    if ($user->getRank() != FractionManager::INDEPENDENT && $this->getMain()->getUser($entity->getName())->getFraction() == $user->getFraction()) {
                        $event->setCancelled();
                    }
                }/* elseif ($entity->getDataPropertyManager()->hasProperty('rivex_core_fraction')) {
                    $fraction = $entity->getDataPropertyManager()->getString('rivex_core_fraction');
                    if ($user->getFraction() == $fraction) {
                        $event->setCancelled();
                        $this->getMain()->getWindows()->getByName('generatormenufraction')->show($damager);
                    } else {
                        $damager->getLevel()->addParticle(new DestroyBlockParticle($entity->add(0, 2), Block::get(Block::REDSTONE_BLOCK)));
                        $damager->sendPopup('§aЗдоровье: §c' . $entity->getHealth() . '/' . $entity->getMaxHealth());
                    }
                }*/
            }
        }
    }

    public function onMotion(EntityMotionEvent $event)
    {
        if ($event->getEntity() instanceof Hunkey) {
            $event->setCancelled();
        }
    }

    public function onGameModeChange(PlayerGameModeChangeEvent $event)
    {
        $event->getPlayer()->getInventory()->clearAll();
        $event->getPlayer()->setCurrentTotalXp(0);
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event)
    {
        if ($event->getTransaction()->getSource()->getGamemode() != Player::SURVIVAL) {
            foreach ($event->getTransaction()->getInventories() as $inventory) {
                if (!($inventory instanceof PlayerInventory or $inventory instanceof PlayerCursorInventory)) {
                    $event->setCancelled();
                    return;
                }
            }
        }
    }
	
	public function onLevelLoad(LevelLoadEvent $event)
	{
		$point = $this->getMain()->getConfig()->get('spawn-point', array('x' => null, 'y' => null, 'z' => null));
		if ($point['x'] !== null && $point['y'] !== null && $point['z'] !== null) {
			$event->getLevel()->setSpawnLocation(new Vector3($point['x'], $point['y'], $point['z']));
		}
	}

    /**
     * @return Main
     */
    public function getMain()
    {
        return $this->main;
    }

}
