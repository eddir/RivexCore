<?php

namespace rivex\rivexcore\modules\window\primal;

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
 * June 2018
 */

use pocketmine\Player;


use rivex\rivexcore\Main;

use rivex\rivexcore\modules\window\element\Label;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;
use rivex\rivexcore\modules\window\BaseWindow;

class AccountWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("§9Аккаунт");
        parent::__construct($id, 'account');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        $this->ui->clean();
        $this->ui->addElement(new Label(
            "§2Ваш никнейм: " . $player->getName() .
            "\nВы находитесь на сервере: " . Main::getServerName()
        ));
        $this->serialize();
        return true;
    }

    /**
     * @param Player $player
     * @param $data
     * @return bool|mixed|void
     */
    public function handle(Player $player, $data)
    {
        Main::getInstance()->getWindows()->getByName("help")->show($player);
    }
}
    
