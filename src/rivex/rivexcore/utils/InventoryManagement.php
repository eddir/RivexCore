<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/4/18
 * Time: 10:42 PM
 */

namespace rivex\rivexcore\utils;


use pocketmine\inventory\Inventory;
use pocketmine\item\Item;


class InventoryManagement
{

    public static function getCapacityOf(Item $item, Inventory $inventory): int
    {
        $count = 0;
        for ($i = 0, $size = $inventory->getSize(); $i < $size; ++$i) {
            $slot = $inventory->getItem($i);
            if ($item->equals($slot)) {
                if (($diff = $slot->getMaxStackSize() - $slot->getCount()) > 0) {
                    $count += $diff;
                }
            } elseif ($slot->isNull()) {
                $count += $inventory->getMaxStackSize();
            }
        }
        return $count;
    }

    public static function getCountOf(Item $item, Inventory $inventory): int
    {
        $count = 0;
        foreach ($inventory->getContents() as $slot) {
            if ($slot->getId() == $item->getId()) {
                $count += $slot->getCount();
            }
        }
        return $count;
    }

    public static function removeFromInventory(Item $item, Inventory $inventory)
    {
        foreach ($inventory->getContents() as $slot => $content) {
            if ($content->getId() == $item->getId()) {
                if ($content->getCount() >= $item->getCount()) {
                    $inventory->setItem($slot, Item::get($item->getId(), $item->getDamage(), $content->getCount() - $item->getCount()));
                    $item->setCount(0);
                    return true;
                } else {
                    $inventory->setItem($slot, Item::get(0));
                    $item->setCount($item->getCount() - $content->getCount());
                }
            }
        }
        return false;
    }

}