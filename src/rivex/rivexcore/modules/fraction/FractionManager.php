<?php

namespace rivex\rivexcore\modules\fraction;

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
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\fraction\entity\Hunkey;
use rivex\rivexcore\modules\fraction\task\GeneratorTask;
use rivex\rivexcore\User;

class FractionManager
{

    private $main;

    public const INDEPENDENT = 0;
    public const LEADER = 1;
    public const DEPUTY = 2;
    public const MEMBER = 3;

    private $sessions = array();

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

    public function create($name, User $leader, $description = null)
    {
        $name = strtolower($name);
        $this->getMain()->getDbLocal()->query("INSERT INTO `fractions` (`name`, `description`) VALUES (#s, #s)", $name, $description);
        $leader->setFraction($name, 1);
    }

    public function getFraction($player)
    {
        if ($user = $this->getMain()->getUser($player)) {
            return $user->getFraction();
        }
        return null;
    }

    public function getRank($player)
    {
        if ($user = $this->getMain()->getUser($player)) {
            return $user->getRank();
        }
        return null;
    }

    public function setRank($player, $rank)
    {
        $this->getMain()->getDbLocal()->query("UPDATE `users` SET `rank` = #d WHERE `name` = #s", $rank, $player);
        if (($user = $this->getMain()->getUser($player))) {
            $user->setFraction($user->getFraction(), $rank, false);
        }
    }

    public function exists($fraction)
    {
        return $this->getMain()->getDbLocal()->exists("SELECT * FROM `fractions` WHERE `fractions`.`name` = #s", strtolower($fraction));
    }

    public function addInvite($user, $fraction)
    {
        $this->getMain()->getDbLocal()->query('INSERT INTO invites (fraction, user) VALUES(#s, #s)', strtolower($fraction), strtolower($user));
    }

    public function getInvites($fraction)
    {
        return $this->getMain()->getDbLocal()->fetch_array('SELECT fraction, user FROM `invites` WHERE `invites`.`fraction` = #s', $fraction);
    }

    public function acceptInvite($user, $fraction, $rank = 2)
    {
        $this->removeInvite($user, $fraction);
        $this->join($user, $fraction, $rank);
    }

    public function removeInvite($user, $fraction)
    {
        $this->getMain()->getDbLocal()->query('DELETE FROM `invites` WHERE user = #s AND fraction = #s', strtolower($user), strtolower($fraction));
    }

    public function getMembers($fraction)
    {
        return $this->getMain()->getDbLocal()->fetch_array('SELECT name, rank FROM `users` WHERE `users`.`fraction` = #s', $fraction);
    }


    /**
     *
     * Килы + смерти
     *
     * ордер
     *
     * SELECT
     * name, description,
     * (SELECT
     * (SELECT SUM(kills) FROM users WHERE users.fraction = fractions.name) +
     * (SELECT SUM(deaths) FROM users WHERE users.fraction = fractions.name)
     * ) AS kd
     * FROM fractions GROUP BY name, description ORDER BY kd DESC
     *
     * SELECT name,
     */
    public function getTop()
    {
        $top = $this->getMain()->getDbLocal()->fetch_array('SELECT name, description, (SELECT (SELECT SUM(kills) FROM users WHERE users.fraction = fractions.name) + (SELECT SUM(deaths) FROM users WHERE users.fraction = fractions.name) ) AS kd FROM fractions GROUP BY name, description ORDER BY kd DESC LIMIT 30');
        //TODO!
        return $top;
    }

    public function getKills($fraction)
    {
        return $this->getMain()->getDbLocal()->fetch_one('SELECT sum(`kills`)  as `kills` FROM `users` WHERE `users`.`fraction` = #s', $fraction)['kills'];
    }

    public function getDeaths($fraction)
    {
        return $this->getMain()->getDbLocal()->fetch_one('SELECT sum(`deaths`)  as `deaths` FROM `users` WHERE `users`.`fraction` = #s', $fraction)['deaths'];
    }

    public function join($user, $fraction, $rank = 2)
    {
        $this->getMain()->getDbLocal()->query('UPDATE `users` SET `fraction` = #s, `rank` = 2 WHERE `name` = #s', $fraction, $user);
    }

    public function leave($username)
    {
        if (($user = Main::getInstance()->getUser($username))) {
            $user->leaveFraction();
        } else {
            $this->getMain()->getDbLocal()->query('UPDATE `users` SET `fraction` = null, `rank` = 0 WHERE `name` = #s', $username);
        }
    }

    public function getLeader($fraction)
    {
        $leader = $this->getMain()->getDbLocal()->fetch_one('SELECT `name` FROM `users` WHERE `fraction` = #s AND `rank` = 1', strtolower($fraction));
        return isset($leader['name']) ? $leader['name'] : null;
    }

    public function remove($fraction)
    {
        $this->getMain()->getDbLocal()->query('UPDATE `users` SET `fraction` = null WHERE `fraction` = #s', $fraction);
        $this->getMain()->getDbLocal()->query('DELETE FROM `fractions` WHERE `name` = #s', $fraction);
        foreach (Main::getInstance()->getUsers() as $user) {
            /** @var $user User */
            if ($user->getFraction() == $fraction) {
                $user->setFraction(null, 0, false);
                $user->getPlayer()->sendMessage('§eГлава фракции распустил ваш клан');
            }
        }
    }

    public function isGeneratorCreated($fraction)
    {
        $data = $this->getNeedsForGenerator($fraction);
        if ($data['generator_need_wood'] == 0 && $data['generator_need_cobblestone'] == 0) {
            return true;
        }
        return false;
    }

    public function setGeneratorAlive($fraction)
    {
        $this->getMain()->getDbLocal()->query('UPDATE `fractions` SET `generator_alive` = 1 WHERE `name` = #s', $fraction);
    }

    public function removeGenerator($fraction)
    {
        $this->getMain()->getDbLocal()->query('UPDATE `fractions` SET `generator_need_wood` = DEFAULT(`generator_need_wood`), `generator_need_cobblestone` = DEFAULT(`generator_need_cobblestone`), `generator_wood` = 0, `generator_cobblestone` = 0, `generator_alive` = 0, `generator_id` = null WHERE `name` = #s', $fraction);
    }

    public function isGeneratorAlive($fraction)
    {
        $response = $this->getMain()->getDbLocal()->fetch_one('SELECT `generator_alive` FROM `fractions` WHERE `name` = #s', $fraction);
        return isset($response['generator_alive']) ? $response['generator_alive'] : false;
    }

    /**
     * @param string $fraction
     * @return Entity|null
     */
    public function getGenerator($fraction)
    {
        //TODO
        return null;
    }

    public function getGeneratorCollect($fraction)
    {
        return $this->getMain()->getDbLocal()->fetch_one('SELECT `generator_wood`, `generator_cobblestone` FROM `fractions` WHERE `name` = #s', $fraction);
    }

    public function getNeedsForGenerator($fraction)
    {
        return $this->getMain()->getDbLocal()->fetch_one('SELECT `generator_need_wood`, `generator_need_cobblestone` FROM `fractions` WHERE `name` = #s', $fraction);
    }

    public function addToGenerator($fraction, $wood, $cobblestone)
    {
        if ($wood > 0) {
            $this->getMain()->getDbLocal()->query('UPDATE `fractions` SET `generator_need_wood` = `generator_need_wood` - #d WHERE `name` = #s', $wood, $fraction);
        }
        if ($cobblestone > 0) {
            $this->getMain()->getDbLocal()->query('UPDATE `fractions` SET `generator_need_cobblestone` = `generator_need_cobblestone` - #d WHERE `name` = #s', $cobblestone, $fraction);
        }
    }

    public function removeItemsFromGenerator($fraction, $items)
    {
        $this->getMain()->getDbLocal()->query('UPDATE `fractions` SET `generator_cobblestone` = `generator_cobblestone` - #d, `generator_wood` = `generator_wood` - #d WHERE `name` = #s', $items['generator_cobblestone'], $items['generator_wood'], $fraction);
    }

    public function setSession(User $user)
    {
        $this->sessions[$user->getPlayer()->getLowerCaseName()] = $user;
    }

    public function getSession($player)
    {
        return isset($this->sessions[strtolower($player)]) ? $this->sessions[strtolower($player)] : false;
    }

    public function destroySession($player)
    {
        if (isset($this->sessions[strtolower($player)])) {
            unset($this->sessions[strtolower($player)]);
        }
    }

    /**
     * @param Position $position
     * @param string $fraction
     * @param int $yaw
     * @param int $pitch
     */
    public function spawnHunkey(Position $position, string $fraction, $yaw = 0, $pitch = 0)
    {
        $uid = ++$this->getMain()->getDbLocal()->fetch_one('SELECT MAX(`generator_id`) AS `uid` FROM `fractions`')['uid'];
        $this->getMain()->getDbLocal()->query('UPDATE `fractions` SET `generator_alive` = 1, `generator_id` = #d WHERE `name` = #s', $uid, $fraction);
        $nbt = new CompoundTag();
        $nbt->setTag(new ListTag("Pos", [new DoubleTag("", $position->getX() + 0.5), new DoubleTag("", $position->getY()), new DoubleTag("", $position->getZ() + 0.5)]));
        $nbt->setTag(new ListTag("Motion", [new DoubleTag("", 0), new DoubleTag("", 0), new DoubleTag("", 0)]));
        $nbt->setTag(new ListTag("Rotation", [new FloatTag("", $yaw), new FloatTag("", $pitch)]));
        $nbt->setTag(new FloatTag("HealF", 500.0));
        $nbt->setTag(new StringTag("fraction", $fraction));
        $entity = new Hunkey($position->getLevel(), $nbt, $this->getMain()->getDataFolder() . 'skins/Hunkey.bin', $uid);
        $entity->setNameTag('§aЖитель ' . $fraction);
        $entity->setNameTagVisible(true);
        $entity->setNameTagAlwaysVisible(true);
        $entity->spawnToAll();
    }

}
