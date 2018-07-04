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

use pocketmine\entity\Entity;

use pocketmine\item\Item;
use pocketmine\Player;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\fraction\FractionManager;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Label;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;
use rivex\rivexcore\utils\InventoryManagement;

class GeneratorFractionWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Генератор");
        parent::__construct($id, 'generatorfraction');
    }

    public function choice()
    {
        return Main::getInstance()->getWindows()->add(self::class);
    }

    public function prepare(Player $player)
    {
        $this->ui->clean();
        $user = Main::getInstance()->getUser($player->getName());
        $fractions = Main::getInstance()->getFractions();
        if ($user->getRank() != FractionManager::INDEPENDENT) {
            if ($user->getRank() == FractionManager::LEADER) {
                if ($fractions->isGeneratorCreated($user->getFraction())) {
                    $this->ui->addElement(new Label('Ресурсы собраны. Нажмите ниже, чтобы получить яйцо призывания жителя, который будет добывать ресурсы. Установите его в надёждное место.'));
                    //или кнопку: установить житнля в другое место? нет, наверное нет
                } else {
                    $needs = $fractions->getNeedsForGenerator($user->getFraction());
                    $this->ui->addElement(new Label('Попросите участников фракции собрать ресурсы для жителя. В будущем он будет приносить вам ресурсы. Участники клана могут сдавать свои ресурсы командой /fraction'));
                    $this->ui->addElement(new Label("Осталось собрать:\n§eДерево: §a" . $needs['generator_need_wood'] . "\n§eБулыжник: §a" . $needs['generator_need_cobblestone']));
                    $user_wood = InventoryManagement::getCountOf(Item::get(Item::WOOD), $player->getInventory());
                    $user_cobblestone = InventoryManagement::getCountOf(Item::get(Item::COBBLESTONE), $player->getInventory());
                    if ($user_wood > $needs['generator_need_wood']) {
                        $user_wood = $needs['generator_need_wood'];
                    }
                    if ($user_cobblestone > $needs['generator_need_cobblestone']) {
                        $user_cobblestone = $needs['generator_need_cobblestone'];
                    }
                    $this->ui->addElement(new Label('Нажмите ниже для того, чтобы сдать ' . $user_wood . ' дерево и ' . $user_cobblestone . ' булыжник.'));
                }
            } else {
                if ($fractions->isGeneratorCreated($user->getFraction())) {
                    $this->ui->addElement(new Label('Спросите у лидера фракции, где расположен житель.'));
                } else {
                    $needs = $fractions->getNeedsForGenerator($user->getFraction());
                    $this->ui->addElement(new Label("Осталось собрать:\n§eДерево: §a" . $needs['generator_need_wood'] . "\n§eБулыжник: §a" . $needs['generator_need_cobblestone']));
                    $user_wood = InventoryManagement::getCountOf(Item::get(Item::WOOD), $player->getInventory());
                    $user_cobblestone = InventoryManagement::getCountOf(Item::get(Item::COBBLESTONE), $player->getInventory());
                    if ($user_wood > $needs['generator_need_wood']) {
                        $user_wood = $needs['generator_need_wood'];
                    }
                    if ($user_cobblestone > $needs['generator_need_cobblestone']) {
                        $user_cobblestone = $needs['generator_need_cobblestone'];
                    }
                    $this->ui->addElement(new Label('Нажмите ниже для того, чтобы сдать ' . $user_wood . ' дерево и ' . $user_cobblestone . ' булыжник.'));
                }
            }
        } else {
            return false;
        }
        $this->serialize();
        return true;
    }

    public function handle(Player $player, $response)
    {
        $user = Main::getInstance()->getUser($player->getName());
        if ($user->getFraction()) {
            $fractions = Main::getInstance()->getFractions();
            if (!$fractions->isGeneratorCreated($user->getFraction())) {
                $needs = $fractions->getNeedsForGenerator($user->getFraction());
                $user_wood = InventoryManagement::getCountOf(Item::get(Item::WOOD), $player->getInventory());
                $user_cobblestone = InventoryManagement::getCountOf(Item::get(Item::COBBLESTONE), $player->getInventory());
                if (($user_wood > 0 || $user_cobblestone > 0) && $player->getGamemode() == Player::SURVIVAL) {
                    if ($user_wood > $needs['generator_need_wood']) {
                        $user_wood = $needs['generator_need_wood'];
                    }
                    if ($user_cobblestone > $needs['generator_need_cobblestone']) {
                        $user_cobblestone = $needs['generator_need_cobblestone'];
                    }
                    $fractions->addToGenerator($user->getFraction(), $user_wood, $user_cobblestone);
                    $progress_wood = InventoryManagement::removeFromInventory(Item::get(Item::WOOD, 0, $user_wood), $player->getInventory());
                    $progress_cobblestone = InventoryManagement::removeFromInventory(Item::get(Item::COBBLESTONE, 0, $user_cobblestone), $player->getInventory());
                    if ($user_wood == $needs['generator_need_wood'] && $user_cobblestone == $needs['generator_need_cobblestone']) {
                        $player->sendMessage('§eПринято. Лидер фракции может теперь призвать жителя!');
                    } else {
                        $player->sendMessage('§eПринято §aДерево: ' . $user_wood . ' Булыжник: ' . $user_cobblestone);
                    }
                } else {
                    $player->sendMessage('§eУ вас нет булыжника или дерева.');
                }
            } else {
                if ($user->getRank() == FractionManager::LEADER) {
                    $player->getInventory()->addItem(Item::get(Item::SPAWN_EGG, Entity::VILLAGER, 1)->setCustomName('§a§lЯйцо призывания генератора'));
                    $fractions->setSession($user);
                    $player->sendMessage('§eНайдите защищённое место для установки генератора');
                }
            }
        }
        return true;
    }

}
