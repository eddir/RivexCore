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
use rivex\rivexcore\modules\fraction\FractionManager;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\type\Modal;
use rivex\rivexcore\modules\window\Window;

class LeaveFractionWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Modal("Выйти из фракции", "Вы уверены, что хотите выйти из фракции?", "Да", "Нет");
        parent::__construct($id, 'leavefraction');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        return true;
    }

    public function handle(Player $player, $response)
    {
        $user = Main::getInstance()->getUser($player->getName());
        if ($user->getRank() != FractionManager::INDEPENDENT) {
            if ($response) {
                Main::getInstance()->getFractions()->leave($player->getLowerCaseName());
                $player->sendMessage('§eВы вышли из клана');
            } else {
                Main::getInstance()->getWindows()->getByName("fraction")->show($player);
            }
        } else {
            $player->sendMessage("§eВы не являетесь участником фракции");
            return false;
        }
        return true;
    }

}
