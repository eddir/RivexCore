<?php

namespace rivex\rivexcore\modules\window\type;

/**
 * Окно с кнопками "да" и "нет"
 *
 * @author thebigsmilexd
 */

use pocketmine\Player;
use rivex\rivexcore\modules\window\element\Element;

class Modal implements WindowType, \JsonSerializable
{
    /** @var string */
    protected $title = '';
    /** @var string */
    protected $content = '';
    /** @var string */
    protected $trueButtonText = '';
    /** @var string */
    protected $falseButtonText = '';
    /** @var int */
    private $id;

    /**
     * This is a window to show a simple text to the player
     *
     * @param string $title
     * @param string $content
     * @param string $trueButtonText
     * @param string $falseButtonText
     */
    public function __construct($title, $content, $trueButtonText, $falseButtonText)
    {
        $this->title = $title;
        $this->content = $content;
        $this->trueButtonText = $trueButtonText;
        $this->falseButtonText = $falseButtonText;
    }

    final public function jsonSerialize()
    {
        return [
            'type' => 'modal',
            'title' => $this->title,
            'content' => $this->content,
            'button1' => $this->trueButtonText,
            'button2' => $this->falseButtonText,
        ];
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
     * @return boolean depending on which button was clicked
     */
    final public function handle($response, Player $player)
    {
        return "";
    }

    final public function getTitle()
    {
        return $this->title;
    }

    public function getContent(): array
    {
        return [$this->content, $this->trueButtonText, $this->falseButtonText];
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
        return null;
    }

    public function setElement(Element $element, int $index)
    {
    }
}
