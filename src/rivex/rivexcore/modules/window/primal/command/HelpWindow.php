<?php

namespace rivex\rivexcore\modules\window\primal\command;

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
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Button;
use rivex\rivexcore\modules\window\type\Menu;
use rivex\rivexcore\modules\window\Window;

class HelpWindow extends BaseWindow implements Window
{

    const FIRST_BOTTOM = 0;

    public function __construct($id)
    {
        $this->ui = new Menu("Меню");
        $this->ui->addButton(new Button("Button.1"));
        $this->ui->addButton(new Button("Button.2"));
        parent::__construct($id, 'help');
    }

    public function choice()
    {
        return $this;
    }

    public function handle(Player $player, $response)
    {
        \rivex\rivexcore\Main::getInstance()->getWindows()->getByName('login')->show($player);
    }

}
