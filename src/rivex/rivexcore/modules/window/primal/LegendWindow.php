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

class LegendWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("О сервере");
        $this->ui->addElement(new Label("Добро пожаловать!\nРады тебя видеть на наших серверах! Используй вкладку 'Сменить сервер' для дальнейшей игры."));
        parent::__construct($id, 'legend');
    }

    public function choice()
    {
        return $this;
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
    
