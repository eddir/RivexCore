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
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;
use rivex\rivexcore\User;

class StatusFractionWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Статус фракции");
        parent::__construct($id, 'statusfraction');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        $this->ui->clean();
        $user = Main::getInstance()->getUser($player->getName());
        $fractions = Main::getInstance()->getFractions();
        if ($user->getFraction()) {
            $this->ui->addElement(new Label(
                "Фракция §e" . $user->getFraction() .
                "\n§aУбийства: " . $fractions->getKills($user->getFraction()) .
                "\nСмерти: " . $fractions->getDeaths($user->getFraction()) .
                "\nУчастники: "
            ));
            $members = $fractions->getMembers($user->getFraction());
            foreach ($members as $member) {
                $this->ui->addElement(new Label($member['name'] . ' §7(' . User::getStringRank($member['rank']) . ')'));
            }
        } else {
            $this->ui->addElement(new Label("Вы не состоите ни в одном клане!"));
        }
        $this->serialize();
        return true;
    }

    public function handle(Player $player, $response)
    {
        Main::getInstance()->getWindows()->getByName('fraction')->show($player);
        return true;
    }

}
