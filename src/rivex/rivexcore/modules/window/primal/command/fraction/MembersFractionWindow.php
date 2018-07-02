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
use rivex\rivexcore\modules\window\element\Dropdown;
use rivex\rivexcore\modules\window\element\Label;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;

class MembersFractionWindow extends BaseWindow implements Window
{

    protected $sessions = array();

    public function __construct($id)
    {
        $this->ui = new Custom("Участники клана");
        parent::__construct($id, 'membersfraction');
    }

    public function choice()
    {
        return Main::getInstance()->getWindows()->add(self::class);
    }

    public function prepare(Player $player)
    {
        $this->ui->clean();
        $user = Main::getInstance()->getUser($player->getName());
        if ($user->getRank() == 1) {
            $this->ui->addElement(new Label("Здесь вы можете менять ранги учасьников, а также принимать и увольнять из фракции"));

            $index = 1;
            $members = Main::getInstance()->getFractions()->getMembers($user->getFraction());
            foreach ($members as $member) {
                if ($member['name'] == $player->getLowerCaseName()) {
                    continue;
                }
                $this->sessions[$player->getName()][$index++] = array($member['name'], false);
                $this->ui->addElement(new Dropdown(
                    $member['name'],
                    array(
                        'Участник',
                        'Заместитель',
                        'Отчислить'
                    ),
                    $this->encodeRank($member['rank'])
                ));
            }

            $invites = Main::getInstance()->getFractions()->getInvites($user->getFraction());
            if (isset($invites[0])) {
                $index++;
                $this->ui->addElement(new Label('Заявки'));
                foreach ($invites as $i) {
                    $this->sessions[$player->getName()][$index++] = array($i['user'], true);
                    $this->ui->addElement(new Dropdown(
                        $i['user'],
                        array(
                            'Участник',
                            'Заместитель',
                            'Отклонить заявку'
                        ),
                        0
                    ));
                }
            } else {
                $this->ui->addElement(new Label("Нет активных заявок на вступление"));
            }
        } else {
            $player->sendMessage("§eЭто окно доступно только лидерам!");
            return false;
        }
        $this->serialize();
        return true;
    }

    public function encodeRank($rank)
    {
        switch ($rank) {
            case FractionManager::INDEPENDENT:
                return 2;
            case FractionManager::DEPUTY:
                return 1;
            default:
                return 0;
        }
    }

    public function decodeRank($rank)
    {
        switch ($rank) {
            case 0:
                return FractionManager::MEMBER;
            case 1:
                return FractionManager::DEPUTY;
            default:
                return FractionManager::INDEPENDENT;
        }
    }

    public function handle(Player $player, $response)
    {
        if (isset($this->sessions[$player->getName()])) {
            $user = Main::getInstance()->getUser($player->getName());
            $fraction = $user->getFraction();
            foreach ($this->sessions[$player->getName()] as $index => $data) {
                $rank = $this->decodeRank($response[$index]['index']);
                if ($data[0] == $player->getLowerCaseName()) {
                    continue;
                }
                if ($data[1]) {
                    if ($rank == FractionManager::INDEPENDENT) {
                        Main::getInstance()->getFractions()->removeInvite($data[0], $user->getFraction());
                        if (($inviting = Main::getInstance()->getUser($data[0]))) {
                            $inviting->getPlayer()->sendMessage('§eВаша заявка во фракцию ' . $fraction . ' отклонена.');
                        }
                    } else {
                        Main::getInstance()->getFractions()->acceptInvite($data[0], $user->getFraction(), $rank);
                        if (($inviting = Main::getInstance()->getUser($data[0]))) {
                            $inviting->getPlayer()->sendMessage('§eВы приняты в фракцию ' . $fraction);
                        }
                    }
                } else {
                    if ($rank == FractionManager::INDEPENDENT) {
                        Main::getInstance()->getFractions()->leave($data[0]);
                        if (($inviting = Main::getInstance()->getUser($data[0]))) {
                            $inviting->getPlayer()->sendMessage('§eВы отчислены из фракции ' . $fraction);
                        }
                    } else {
                        Main::getInstance()->getFractions()->setRank($data[0], $rank);
                    }
                }
            }
            $player->sendMessage("§eСостав клана обновлён");
        }
        return true;
    }

}
