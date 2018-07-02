<?php

namespace rivex\rivexcore\modules\window\primal\command\fraction;

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

class FractionWindow extends BaseWindow implements Window
{

    private $uis = array();

    public function __construct($id)
    {
        //Для тех, кто не состоит во фракции
        $this->uis[0] = new Menu("Фракции");
        $this->uis[0]->addButton(new Button("Создать фракцию"));
        $this->uis[0]->addButton(new Button("Вступить в фракцию"));
        $this->uis[0]->addButton(new Button("Топ"));

        //Для участников фракции
        $this->uis[1] = new Menu("Фракция");
        $this->uis[1]->addButton(new Button("Статус фракции"));
        $this->uis[1]->addButton(new Button("Генератор"));
        $this->uis[1]->addButton(new Button("Топ"));
        $this->uis[1]->addButton(new Button("Покинуть фракцию"));

        //Для лидеров
        $this->uis[2] = new Menu("Фракция");
        $this->uis[2]->addButton(new Button("Статус фракции"));
        $this->uis[2]->addButton(new Button("Участники"));
        $this->uis[2]->addButton(new Button("Генератор"));
        $this->uis[2]->addButton(new Button("Топ"));
        $this->uis[2]->addButton(new Button("Распустить фракцию"));

        parent::__construct($id, 'fraction');
    }

    public function choice()
    {
        return $this;
    }

    /**
     * И тут проблема.... Как обработать вывод для пользователя, когда есть 3 сценария,
     * но нет метода их отслеживания?
     * Наверное надо хранить сессии окон в классе User и выводить персональные окна? Точно!
     * @param Player $player
     * @return bool
     */
    public function prepare(Player $player)
    {
        $user = Main::getInstance()->getUser($player->getName());
        if ($user->getRank() == 0) {
            $this->ui = $this->uis[0];
            $this->sessions[$player->getLowerCaseName()] = array('item' => 0);
        } elseif ($user->getRank() == 1) {
            $this->ui = $this->uis[2];
            $this->sessions[$player->getLowerCaseName()] = array('item' => 2);
        } else {
            $this->ui = $this->uis[1];
            $this->sessions[$player->getLowerCaseName()] = array('item' => 1);
        }
        $this->serialize();
        return true;
    }

    public function handle(Player $player, $response)
    {
        //если админ, то он мог нажать на первую кнопку и ему нужно вывести следующее окно
        //и хандл будет другой согласно $this->ui
        //хорошо. Это будет здесь обрабатываться
        if (isset($this->sessions[$player->getLowerCaseName()])) {
            switch ($this->sessions[$player->getLowerCaseName()]['item']) {
                case 0:
                    switch ($response) {
                        case 0:
                            Main::getInstance()->getWindows()->getByName('createfraction')->show($player);
                            break;
                        case 1:
                            Main::getInstance()->getWindows()->getByName('joinfraction')->show($player);
                            break;
                        case 2:
                            Main::getInstance()->getWindows()->getByName('topfraction')->show($player);
                            break;
                    }
                    break;
                case 1:
                    switch ($response) {
                        case 0:
                            Main::getInstance()->getWindows()->getByName('statusfraction')->show($player);
                            break;
                        case 1:
                            Main::getInstance()->getWindows()->getByName('generatorfraction')->show($player);
                            break;
                        case 2:
                            Main::getInstance()->getWindows()->getByName('topfraction')->show($player);
                            break;
                        case 3:
                            Main::getInstance()->getWindows()->getByName('leavefraction')->show($player);
                            break;
                    }
                    break;
                case 2:
                    switch ($response) {
                        case 0:
                            Main::getInstance()->getWindows()->getByName('statusfraction')->show($player);
                            break;
                        case 1:
                            Main::getInstance()->getWindows()->getByName('membersfraction')->show($player);
                            break;
                        case 2:
                            Main::getInstance()->getWindows()->getByName('generatorfraction')->show($player);
                            break;
                        case 3:
                            Main::getInstance()->getWindows()->getByName('topfraction')->show($player);
                            break;
                        case 4:
                            Main::getInstance()->getWindows()->getByName('removefraction')->show($player);
                            break;
                    }
                    break;
                default:
                    return false;
            }
            unset($this->sessions[$player->getLowerCaseName()]);
            return true;
        }
        return false;
    }

}
