<?php

namespace rivex\rivexcore\modules\window;

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

use pocketmine\Player;
use rivex\rivexcore\Main;

use rivex\rivexcore\modules\window\network\ModalFormRequestPacket;
use rivex\rivexcore\modules\window\type\WindowType;

abstract class BaseWindow
{
    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var WindowType */
    protected $ui;
    /** @var string */
    protected $formData;
    /** @var callable */
    protected $callable;

    protected $sessions = array();

    public function __construct($id, $name = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->serialize();
    }

    /**
     * @param Player $player
     */
    public function show(Player $player)
    {
        try {
            if ($this->prepare($player)) {
                $pk = new ModalFormRequestPacket();
                $pk->formData = $this->formData;
                $pk->formId = $this->id;
                $player->dataPacket($pk);
            }
        } catch (\Exception $e) {
            $player->sendMessage('§eТехническая неполадка не позволила вам сделать это. Сообщите администраторам код ошибки и они её исправят. Код: SHOW:' . $this->name);
            Main::getInstance()->getLogger()->error($e->getMessage() . ' ' . $e->getFile() . ' -> ' . $e->getLine());
        }
    }

    public function serialize()
    {
        $this->formData = json_encode($this->ui);
    }

    public function handle(Player $player, $response)
    {
        return false;
    }

    public function prepare(Player $player)
    {
        return true;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable|null
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @return WindowType
     */
    public function getUI(): WindowType
    {
        return $this->ui;
    }

}
