<?php

/**
 * API for Minecraft: Bedrock custom UI (forms)
 */

declare(strict_types=1);

namespace rivex\rivexcore\modules\window\type;

use pocketmine\Player;
use rivex\rivexcore\modules\window\element\Button;
use rivex\rivexcore\modules\window\element\Element;

/**
 * Base class for a custom form. Forms are serialized to JSON data to be sent to clients.
 */
interface WindowType
{
    public function handle($response, Player $player);

    public function jsonSerialize();

    /**
     * To handle manual closing
     * @param Player $player
     */
    public function close(Player $player);

    public function getTitle();

    public function getContent(): array;

    /**
     * @param int $index
     * @return Element|Button|null
     */
    public function getElement(int $index);

    public function setElement(Element $element, int $index);

    public function setID(int $id);

    public function getID(): int;
}
