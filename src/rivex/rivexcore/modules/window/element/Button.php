<?php

namespace rivex\rivexcore\modules\window\element;

use pocketmine\Player;

class Button extends Element
{

    const IMAGE_TYPE_PATH = 'path';
    const IMAGE_TYPE_URL = 'url';

    /** @var string May contains 'path' or 'url' */
    protected $imageType = '';
    /** @var string */
    protected $imagePath = '';

    /**
     *
     * @param string $text Button text
     * @param null $value
     */
    public function __construct(string $text, $value = null)
    {
        parent::__construct($text);
        $this->value = $value;
    }

    public function getType(): string
    {
        return "button";
    }

    /**
     * Add image to button
     *
     * @param string $imageType
     * @param string $imagePath
     * @throws \Exception
     */
    public function addImage(string $imageType, string $imagePath)
    {
        if ($imageType != self::IMAGE_TYPE_PATH && $imageType != self::IMAGE_TYPE_URL) {
            throw new \Exception(__CLASS__ . '::' . __METHOD__ . ' Invalid image type');
        }
        $this->imageType = $imageType;
        $this->imagePath = $imagePath;
    }

    /**
     * @return array
     */
    public function serializeElementData(): array
    {
        $data = array();
        if ($this->imageType != '') {
            $data['image'] = [
                'type' => $this->imageType,
                'data' => $this->imagePath
            ];
        }
        return $data;
    }

    public function handle($value, Player $player)
    {
        return is_null($this->value) ? $value : $this->value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

}
