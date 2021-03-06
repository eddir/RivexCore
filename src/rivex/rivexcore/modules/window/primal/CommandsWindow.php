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

class CommandsWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Доступные команды");
		$this->ui->addElement(new Label(
			"§b/rg help §7- §2помощь по приватам".
			"\n§b/menu §7- §2меню сервера".
			"\n§b/spawn §7- §2вернуться на спавн"
		));
        parent::__construct($id, 'commands');
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
    
