<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/6/18
 * Time: 9:27 PM
 */

namespace rivex\rivexcore\modules\window\primal\command;


use pocketmine\Player;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Button;
use rivex\rivexcore\modules\window\type\Menu;
use rivex\rivexcore\modules\window\Window;

class DeletehomeWindow extends BaseWindow implements Window
{
    public function __construct($id)
    {
        $this->ui = new Menu("Выберите точку для удаления");
        parent::__construct($id, 'deletehome');
    }

    public function prepare(Player $player)
    {
        $this->ui->clean();
        $homes = Main::getInstance()->getUser($player->getName())->getHomes();
        if (count($homes) > 0) {
            foreach ($homes as $home) {
                $this->ui->addButton(new Button($home['name']));
            }
        } else {
            $this->ui->addButton(new Button('У Вас нет ни одной точки дома. Нажмите, что создать новую'));
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
        $user = Main::getInstance()->getUser($player->getName());
        $homes = $user->getHomes();
        if (isset($homes[$response])) {
            $user->removeHome($homes[$response]['id']);
            $player->sendMessage('§bТочка под названием §e' . $homes[$response]['name'] . ' §bудалена.');
        }
    }
}