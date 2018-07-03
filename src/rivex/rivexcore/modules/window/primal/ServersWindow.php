<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/3/18
 * Time: 11:45 PM
 */

namespace rivex\rivexcore\modules\window\primal;


use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\Player;

use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Button;
use rivex\rivexcore\modules\window\type\Menu;
use rivex\rivexcore\modules\window\Window;

class ServersWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Menu("§9Выберите сервер");
        $port = Main::getInstance()->getServer()->getPort();
        if ($port == 19132 or $port == 19134) {
            $this->ui->addButton(new Button("§2Хаб §e(Вы здесь)"));
            $this->ui->addButton(new Button("§2Космическое выживание"));
        } else {
            $this->ui->addButton(new Button("§2Хаб"));
            $this->ui->addButton(new Button("§2Космическое выживание §e(Вы здесь)"));
        }
        parent::__construct($id, 'servers');
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
        $port = Main::getInstance()->getServer()->getPort();
        switch ($response) {
            case 0:
                if ($port == 19132 or $port == 19134) {
                    $player->sendMessage("§bВы уже находитесь на этом сервере!");
                } else {
                    $pk = new TransferPacket();
                    $pk->address = "rivex-serv.ru";
                    $pk->port = 19132;
                    $player->dataPacket($pk);
                }
                break;
            case 1:
                if ($port == 19132 or $port == 19134) {
                    $pk = new TransferPacket();
                    $pk->address = "rivex-serv.ru";
                    $pk->port = 19131;
                    $player->dataPacket($pk);
                } else {
                    $player->sendMessage("§bВы уже находитесь на этом сервере!");
                }
                break;
        }
        return true;
    }
}