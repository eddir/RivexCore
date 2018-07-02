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

use pocketmine\item\Item;
use pocketmine\Player;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\fraction\FractionManager;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Label;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;

class GeneratorMenuFractionWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Генератор");
        parent::__construct($id, 'generatormenufraction');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        $this->ui->clean();
        $user = Main::getInstance()->getUser($player->getName());
        $fractions = Main::getInstance()->getFractions();
        if ($user->getRank() != FractionManager::INDEPENDENT) {
            if ($fractions->isGeneratorAlive($user->getFraction())) {
                $res = $fractions->getGeneratorCollect($user->getFraction());
                $this->ui->addElement(new Label("Житель собрал следующие ресурсы:\n\n§eДерево: §a" . $res['generator_wood'] . "\n§eБулыжник: §a" . $res['generator_cobblestone'] . "\n\n§fНажмите на кнопку, чтобы забрать всё."));
            } else {
                $player->sendMessage('§eПохоже житель мёртв. Попросите лидера создать новый.');
                return false;
            }
        } else {
            $player->sendMessage('§eВы должны состоять в клане для этого действия!');
            return false;
        }
        $this->serialize();
        return true;
    }

    public function handle(Player $player, $response)
    {
        $user = Main::getInstance()->getUser($player->getName());
        $fractions = Main::getInstance()->getFractions();
        if ($user->getRank() != FractionManager::INDEPENDENT) {
            $res = Main::getInstance()->getFractions()->getGeneratorCollect($user->getFraction());
            if ($res['generator_wood'] > 0 || $res['generator_cobblestone'] > 0) {
                $player->getInventory()->addItem(Item::get(Item::WOOD, 0, $res['generator_wood']));
                $player->getInventory()->addItem(Item::get(Item::COBBLESTONE, 0, $res['generator_cobblestone']));
                $fractions->removeItemsFromGenerator($user->getFraction(), $res);
                $player->sendMessage('§eРесурсы выданы!');
            }
        }
        return true;
    }

}
