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

use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Button;
use rivex\rivexcore\modules\window\type\Menu;
use rivex\rivexcore\modules\window\Window;

class HelpWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
	$this->ui = new Menu("§9Выберите пункт меню");
	$this->ui->addButton(new Button("§9Магазин"));
        $this->ui->addButton(new Button("§9Мой профиль"));
        $this->ui->addButton(new Button("§9Легенда"));
        $this->ui->addButton(new Button("§9Сменить сервер"));
        $this->ui->addButton(new Button("§9Мои дома"));
        $this->ui->addButton(new Button("§9Открыть почту"));
        $this->ui->addButton(new Button("§9Доступные мне команды"));
        $this->ui->addButton(new Button("§9Связь с администрацией"));
        parent::__construct($id, 'help');
    }

    public function prepare(Player $player)
    {
        return true;
    }

    public function choice()
    {
        return $this;
    }

    public function handle(Player $player, $response)
    {
        switch ($response) {
	    case 0:
		Main::getInstance()->getServer()->getPluginManager()->getPlugin('RShopSE')->forms->mainShopForm($player);
		break;
	    case 1:
                Main::getInstance()->getWindows()->getByName("account")->show($player);
                break;
            case 2:
                Main::getInstance()->getWindows()->getByName("legend")->show($player);
                break;
            case 3:
                Main::getInstance()->getWindows()->getByName("servers")->show($player);
                break;
            case 4:
                Main::getInstance()->getWindows()->getByName("homes")->show($player);
                break;
            case 5:
                Main::getInstance()->getWindows()->getByName("mailbox")->show($player);
                break;
            case 6:
                Main::getInstance()->getWindows()->getByName("commands")->show($player);
                break;
            case 7:
                Main::getInstance()->getWindows()->getByName("contact")->show($player);
                break;
        }
    }

}
