<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/4/18
 * Time: 10:14 PM
 */

namespace rivex\rivexcore\modules\window\primal;


use pocketmine\item\Item;
use pocketmine\Player;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\type\Modal;
use rivex\rivexcore\modules\window\Window;
use rivex\rivexcore\utils\InventoryManagement;

class ParcelWindow extends BaseWindow implements Window
{
    /** @var int */
    public $transaction;
    /** @var int */
    public $item_id;
    /** @var int */
    public $item_damage;
    /** @var int */
    public $item_amount;
    /** @var string */
    public $description;

    public function __construct($id)
    {
        parent::__construct($id, "parcel");
    }

    public function choice()
    {
        return Main::getInstance()->getWindows()->add(self::class);
    }

    public function prepare(Player $player)
    {
        $this->ui = new Modal('Извещение о посылке',
            'Содержимое: §a' . $this->description . "\n§fКоличество: §a" . $this->item_amount,
            '§fЗабрать',
            '§fНазад'
        );
        $this->serialize();
        return true;
    }

    public function handle(Player $player, $response)
    {
        $capacity = InventoryManagement::getCapacityOf(Item::get($this->item_id, $this->item_damage), $player->getInventory());
        if ($capacity > $this->item_amount) $capacity = $this->item_amount;
        if ($capacity > 0) {
            $player->getInventory()->addItem(Item::get($this->item_id, $this->item_damage, $capacity));
            if ($capacity < $this->item_amount) {
                $lost = $this->item_amount - $capacity;
                Main::getInstance()->getDbGlobal()->query('UPDATE parcels SET `item_amount` = #d WHERE `player` = #s AND `id` = #d',
                    $lost, $player->getName(), $this->transaction);
                $player->sendMessage('§bВыдано §e' . $capacity . ' ' . $this->description . '. Остальную часть Вы сможете забрать, когда освободите свой инвентарь.');
            } else {
                Main::getInstance()->getDbGlobal()->query('DELETE FROM parcels WHERE player = #s AND id = #d', $player->getName(), $this->transaction);
                $player->sendMessage('§bВыдано §e' . $capacity . ' ' . $this->description . '.');
            }
        } else {
            $player->sendMessage('§bВаш инвентарь переполнен.');
        }
        $this->close();
    }
}