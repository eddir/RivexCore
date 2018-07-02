<?php

namespace rivex\rivexcore\modules\window\element;

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

abstract class Element implements \JsonSerializable
{

    protected $text;
    protected $value;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    final public function jsonSerialize(): array
    {
        $data = [
            "type" => $this->getType(),
            "text" => $this->getText()
        ];
        return array_merge($data, $this->serializeElementData());
    }

    abstract public function getType(): string;

    public function getText()
    {
        return $this->text;
    }

    public function handle($value, Player $player)
    {
        return $this->text;
    }

    abstract public function getValue();

    abstract public function setValue($value): void;

    private function serializeElementData()
    {
        return array();
    }

}
