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
use rivex\rivexcore\modules\window\element\Input;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;

class JoinFractionWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Вступление в фракцию");
        $this->ui->addElement(new Input("Название фракции"));
        parent::__construct($id, 'joinfraction');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        $user = Main::getInstance()->getUser($player->getName());
        if ($user->getRank() != 0) {
            $player->sendMessage("§eВыйдите из текущей фракции!");
            return false;
        }
        return true;
    }

    public function handle(Player $player, $response)
    {
        $main = Main::getInstance();
        if (isset($response[0]) && !empty($response[0])) {
            if ($main->getFractions()->exists($response[0])) {
                $main->getFractions()->addInvite($player->getName(), $response[0]);
                $player->sendMessage("§eЗаявка отправлена. Ожидайте подтверждения от лидера.");
                $leader = $main->getUser($main->getFractions()->getLeader($response[0]));
                if (!is_null($leader)) {
                    $leader->getPlayer()->sendMessage("§e" . $player->getName() . " хочет вступить в вашу фракцию. Введите /clan и войдите в раздел участников, чтобы принять его.");
                }
            } else {
                $player->sendMessage("§eФракция не существует или набрана не правильно");
            }
        } else {
            $player->sendMessage("§eВы не ввели название фракции!");
        }
        return true;
    }

}
