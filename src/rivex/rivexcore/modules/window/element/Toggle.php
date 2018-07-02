<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace rivex\rivexcore\modules\window\element;

/**
 * Represents a UI on/off switch. The switch may have a default value.
 */

use pocketmine\Player;

class Toggle extends Element
{
    /** @var bool */
    protected $default;
    /** @var bool */
    protected $value;

    public function __construct(string $text, bool $defaultValue = false)
    {
        parent::__construct($text);
        $this->default = $defaultValue;
    }

    public function getType(): string
    {
        return "toggle";
    }

    /**
     * @return bool
     */
    public function getDefaultValue(): bool
    {
        return $this->default;
    }

    /**
     * @return bool
     */
    public function getValue(): bool
    {
        return $this->value;
    }

    /**
     * @param bool $value
     *
     * @throws \TypeError
     */
    public function setValue($value): void
    {
        if (!is_bool($value)) {
            throw new \TypeError("Expected bool, got " . gettype($value));
        }

        $this->value = $value;
    }


    public function serializeElementData(): array
    {
        return [
            "default" => $this->default
        ];
    }

    public function handle($value, Player $player)
    {
        return $value;
    }

}
