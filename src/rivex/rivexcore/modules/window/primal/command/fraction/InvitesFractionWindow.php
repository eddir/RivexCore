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
use rivex\rivexcore\modules\window\element\Label;
use rivex\rivexcore\modules\window\element\Toggle;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;

class InvitesFractionWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Участники клана");
        parent::__construct($id, 'membersfraction');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        $user = Main::getInstance()->getUser($player->getName());
        if ($user->getRank() == 1) {
            $this->ui->addElement(new Label("Вы можете принять заявки ниже или удалить существующих участников. Для этого установите переключатели в нужное значение и нажмите 'Отправить'."));

            $members = Main::getInstance()->getFractions()->getMembers($user->getFraction());
            foreach ($members as $member) {
                $this->ui->addElement(new Toggle($member['user'], false));
            }

            $invites = Main::getInstance()->getFractions()->getInvites($user->getFraction());
            if (isset($invites[0])) {
                $this->ui->addElement(new Label('Заявки'));
                foreach ($invites as $i) {
                    $this->ui->addElement(new Toggle($i['user'], false));
                }
            } else {
                $this->ui->addElement(new Label("Нет активных заявок на вступление"));
            }
        } else {
            $player->sendMessage("§eЭто окно доступно только лидерам!");
        }
        return true;
    }

    public function handle(Player $player, $response)
    {
        $user = Main::getInstance()->getUser($player->getName());
        if (isset($response[0]) && !empty($response[0])) {
            if (Main::getInstance()->getFractions()->exists($response[0])) {
                Main::getInstance()->getFractions()->addInvite($player->getName(), $response[0]);
                $player->sendMessage("§eЗаявка отправлена. Ожидайте подтверждения от лидера.");
            } else {
                $player->sendMessage("§eФракция не существует или набрана не правильно");
            }
        } else {
            $player->sendMessage("§eВы не ввели название фракции!");
        }
        return true;
    }

}
