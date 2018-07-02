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
 * January 2018
 */

use pocketmine\Player;
use pocketmine\Server;
use rivex\rivexcore\modules\window\element\Input;
use rivex\rivexcore\modules\window\element\Label;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;

class LoginWindow extends PluginWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Авторизация");
        $this->ui->addElement(new Label("Мы рады вас видеть снова!"));
        $this->ui->addElement(new Input("Пароль", "Введите пароль от аккаунта"));
        $this->owner = Server::getInstance()->getPluginManager()->getPlugin('FormAuth');
        parent::__construct($id, 'login');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        $player->sendMessage('§eДля авторизации можно использовать команду §a/l (/login) §eили §a/r (/register) §eдля регистрации');
    }

    public function handle(Player $player, $data)
    {
        // TODO: implement auth api
        /*
        $result = $data[1];
        if ($result === null) {
            $this->getOwner()->reCreateForm($player);
            return true;
        }
        if (!empty($result)) {
            $this->getOwner()->authenticatePlayer($player, $result);
        } else {
            $this->getOwner()->reCreateForm($player);
        }
        return true;
        */
    }
}
