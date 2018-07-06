<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/6/18
 * Time: 8:31 PM
 */

namespace rivex\rivexcore\modules\window\primal\command;


use pocketmine\math\Vector3;
use pocketmine\Player;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Button;
use rivex\rivexcore\modules\window\type\Menu;
use rivex\rivexcore\modules\window\Window;

class HomesWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Menu("Точки дома");
        parent::__construct($id, 'homes');
    }

    public function prepare(Player $player)
    {
        $this->ui->clean();
        $homes = Main::getInstance()->getUser($player->getName())->getHomes();
        $this->ui->addButton(new Button('§2Новую точку'));
        foreach ($homes as $home) {
            $this->ui->addButton(new Button($home['name']));
        }
        $this->serialize();
        return true;
    }

    public function choice()
    {
        return $this;
    }

    public function handle(Player $player, $response)
    {
        if ($response == 0) {
            Main::getInstance()->getWindows()->getByName('sethome')->show($player);
        } else {
            $homes = Main::getInstance()->getUser($player->getName())->getHomes();
            if (isset($homes[$response - 1])) {
                $home = $homes[$response - 1];
                $player->teleport(new Vector3((int) $home['x'], (int) $home['y'], (int) $home['z']));
            }
        }
        return true;
    }
}