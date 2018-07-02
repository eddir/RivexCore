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

class RegisterWindow extends PluginWindow implements Window
{

    public $question;

    public function __construct($id)
    {
        $this->ui = new Custom("Регистрация");
        $this->ui->addElement(new Label("Добро пожаловать на наш сервер! Придумайте пароль, с которым вы будете входить в аккаунт."));
        $this->ui->addElement(new Input("Введите пароль"));
        $this->ui->addElement(new Input("Повторите пароль"));
        $this->owner = Server::getInstance()->getPluginManager()->getPlugin('FormAuth');
        parent::__construct($id, 'register');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        $player->sendMessage('§eДля авторизации можно использовать команду §a/l (/login) §eили §a/r (/register) §eдля регистрации');
    }

    public function handle(Player $player, $response)
    {
        // TODO: implement auth api
        /*
        $result = $response[1];
        $confirm = $response[2];
        if ($result === null) {
            $this->getOwner()->reCreateForm($player);
            return true;
        }
        if (!empty($result) && !empty($confirm)) {
            if ($result == $confirm) {
                $this->getOwner()->registerPlayer($player, $result);
            } else {
                $this->getOwner()->reCreateForm($player);
            }
        } else {
            $this->getOwner()->reCreateForm($player);
        }
        return true;
        */
    }


}
