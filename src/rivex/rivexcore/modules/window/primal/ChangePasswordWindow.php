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

class ChangePasswordWindow extends PluginWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Смена пароля");
        $this->ui->addElement(new Label("Сменить пароль очень просто. Введите его ниже"));
        $this->ui->addElement(new Input("Введите пароль"));
        $this->ui->addElement(new Input("Повторите пароль"));
        $this->owner = Server::getInstance()->getPluginManager()->getPlugin('FormAuth');
        parent::__construct($id, 'changepassword');
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
        // TODO: implement auth api
        /*
        $result = $data[1];
        $confirm = $data[2];
        if ($result === null) {
            return true;
        }
        if (!empty($result) && !empty($confirm)) {
            if ($result == $confirm) {
                $this->getOwner()->changePlayerPassword($player, $result);
            } else {
                return $player->sendMessage("password-password");
            }
        }
        return true;
        */
    }
}
    
