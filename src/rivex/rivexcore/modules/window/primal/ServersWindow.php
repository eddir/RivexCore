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
        $servers = Main::getInstance()->getConfig()->get("servers");
        foreach ($servers as $server) {
            $this->ui->addButton(new Button($server['name']));
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
        $servers = Main::getInstance()->getConfig()->get("servers");
        if (isset($servers[$response])) {
            if ($servers[$response]['transfer'] === true) {
                $pk = new TransferPacket();
                $pk->address = $servers[$response]['ip'];
                $pk->port = $servers[$response]['port'];
                $player->dataPacket($pk);
            } else {
                $player->sendMessage("§bВы уже находитесь на этом сервере!");
            }
        }
        return true;
    }
}