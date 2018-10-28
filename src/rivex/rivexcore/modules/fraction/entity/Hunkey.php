<?php

namespace rivex\rivexcore\modules\fraction\entity;

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

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use rivex\rivexcore\Main;

class Hunkey extends Human
{

    private $uid = 0;
    private $fraction;

    public function getName(): string
    {
        return "Hunkey";
    }

    public function __construct(Level $level, CompoundTag $nbt, $skinPath = null, $uid = null)
    {
        if ($skinPath)
            $this->setSkinData($skinPath);

        parent::__construct($level, $nbt);
        if (!$this->namedtag->hasTag("NameVisibility", IntTag::class)) {
            $this->namedtag->setInt("NatmeVisibility", 2, true);
        }
        $this->setNameTagVisible(true);
        $this->setNameTagAlwaysVisible(true);
        $this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_IMMOBILE, true);
        if (!$this->namedtag->hasTag("Scale", FloatTag::class)) {
            $this->namedtag->setFloat("Scale", 1.0, true);
        }
        $this->getDataPropertyManager()->setPropertyValue(self::DATA_SCALE, self::DATA_TYPE_FLOAT, $this->namedtag->getFloat("Scale"));
        if ($uid)
            $this->setUId($uid);

        if ($this->namedtag->hasTag("uid", IntTag::class)) {
            $this->uid = $this->namedtag->getInt("uid");
        } else {
            $this->kill();
        }
        if (!Main::getInstance()->getDbLocal()->exists('SELECT `name` FROM `fractions` WHERE `generator_id` = #d', $this->uid)) {
            $this->kill();
        }
    }

    protected function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(500);
        parent::initEntity();
        $this->fraction = $this->namedtag->getString("fraction", null);
    }

    public function saveNBT(): CompoundTag
    {
	$nbt = parent::saveNBT();
        $visibility = 2;
        $scale = $this->getDataPropertyManager()->getFloat(Entity::DATA_SCALE);
        $nbt->setInt("NameVisibility", $visibility, true);
	$nbt->setFloat("Scale", $scale, true);
	return $nbt;
    }

    protected function sendSpawnPacket(Player $player): void
    {
        parent::sendSpawnPacket($player);
        $this->sendData($player, [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getDisplayName()]]);
    }

    public function getDisplayName()
    {
        return $this->getNameTag();
    }

    public function setSkinData($path)
    {
        //TODO: setup gd library
        /*
        $img = @imagecreatefrompng($path);
        $bytes = '';
        $l = (int) @getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        */
        $bytes = file_get_contents($path);
        $this->setSkin(new Skin("Standard_CustomSlim", $bytes));
    }

    public function setUId($uid)
    {
        $this->uid = $uid;
        $this->namedtag->setInt('uid', $uid, true);
    }

    public function getUId()
    {
        return $this->uid;
    }

    public function getFraction()
    {
        return $this->fraction;
    }

}
