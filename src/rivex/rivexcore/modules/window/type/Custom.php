<?php

namespace rivex\rivexcore\modules\window\type;

/**
 * Окно с множествами элементами
 *
 * @author thebigsmilexd
 */

use pocketmine\Player;
use rivex\rivexcore\modules\window\element\Button;
use rivex\rivexcore\modules\window\element\Element;

class Custom implements WindowType, \JsonSerializable
{
    /** @var string */
    protected $title = '';
    /** @var array */
    protected $elements = [];
    /** @var string Only for server settings */
    protected $iconURL = '';
    /** @var int */
    private $id;

    /**
     * CustomForm is a totally custom and dynamic form
     * @param $title
     */
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * Add element to form
     * @param Element $element
     */
    public function addElement(Element $element)
    {
        $this->elements[] = $element;
    }

    /**
     * Only for server settings
     * @param string $url
     */
    public function addIconUrl($url)
    {
        $this->iconURL = $url;
    }

    final public function jsonSerialize()
    {
        $data = [
            'type' => 'custom_form',
            'title' => $this->title,
            'content' => []
        ];
        if ($this->iconURL != '') {
            $data['icon'] = [
                "type" => "url",
                "data" => $this->iconURL
            ];
        }
        foreach ($this->elements as $element) {
            $data['content'][] = $element;
        }
        return $data;
    }

    /**
     * To handle manual closing
     * @param Player $player
     */
    public function close(Player $player)
    {
    }

    /**
     * @param array $response
     * @param Player $player
     * @return array containing the options, data, responses etc
     */
    public function handle($response, Player $player)
    {
        $return = [];
        if (!is_array($response)) {
            return null;
        }
        foreach ($response as $elementKey => $elementValue) {
            if (isset($this->elements[$elementKey])) {
                if (!is_null($value = $this->elements[$elementKey]->handle($elementValue, $player))) {
                    $return[] = $value;
                }
            } else {
                error_log(__CLASS__ . '::' . __METHOD__ . " Element with index {$elementKey} doesn't exists.");
            }
        }
        return $return;
    }

    public function clean()
    {
        $this->elements = null;
    }

    final public function getTitle()
    {
        return $this->title;
    }

    public function getContent(): array
    {
        return $this->elements;
    }

    public function setID(int $id)
    {
        $this->id = $id;
    }

    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @param int $index
     * @return Element|null
     */
    public function getElement(int $index)
    {
        return $this->elements[$index];
    }

    public function setElement(Element $element, int $index)
    {
        if ($element instanceof Button) return;
        $this->elements[$index] = $element;
    }
}
