<?php

namespace rivex\rivexcore\modules\window\primal\command\fraction;

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
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\type\Modal;
use rivex\rivexcore\modules\window\Window;

class RemoveFractionWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Modal("Удалить фракцию", "Вы уверены, что хотите удалить фракцию?", "Да", "Нет");
        parent::__construct($id, 'removefraction');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        $user = Main::getInstance()->getUser($player->getName());
        if ($user->getRank() != 1) {
            $player->sendMessage("§eФракцию.может распустить только лидер");
            return false;
        }
        return true;
    }

    public function handle(Player $player, $response)
    {
        $user = Main::getInstance()->getUser($player->getName());
        if ($user->getRank() == 1) {
            if ($response) {
                Main::getInstance()->getFractions()->remove($user->getFraction());
                $player->sendMessage('§eВаша фракция распущена');
            } else {
                $player->sendMessage('§eВаша фракция не тронута');
            }
        } else {
            $player->sendMessage("§eФракцию может распустить только лидер");
            return false;
        }
        return true;
    }

}
