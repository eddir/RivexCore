<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/6/18
 * Time: 9:05 PM
 */

namespace rivex\rivexcore\modules\window\primal\command;


use pocketmine\Player;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Input;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;

class SethomeWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Установка точка дома");
        $this->ui->addElement(new Input("Придумайте название точки"));
        parent::__construct($id, 'sethome');
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
        if (strlen($response[0]) > 0 && strlen($response[0]) < 33) {
            $user = Main::getInstance()->getUser($player->getName());
            if ($user->getHomeCount() < 3) {
                $user->setHome($response[0], $player);
                $player->sendMessage('§aТочка успешно уствновлена!');
            } else {
                $player->sendMessage('§bВы не имеете право владеть больше, чем 3 домами. Удалите лишний командой /delhome .');
            }
        } else {
            $player->sendMessage('§bНекорректное название дома');
        }
        return true;
    }
}