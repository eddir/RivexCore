<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/4/18
 * Time: 8:52 PM
 */

namespace rivex\rivexcore\modules\window\primal;


use pocketmine\Player;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\type\Modal;
use rivex\rivexcore\modules\window\Window;

class MailWindow extends BaseWindow implements Window
{
    /** @var string */
    public $player;
    /** @var string */
    public $sender;
    /** @var string */
    public $body;

    public function __construct($id)
    {
        parent::__construct($id, "mail");
    }

    public function choice()
    {
        return Main::getInstance()->getWindows()->add(self::class);
    }

    public function prepare(Player $player)
    {
        $this->ui = new Modal('Сообщение от ' . $this->sender, $this->body, 'Убрать в архив', 'Назад');
        $this->serialize();
        return true;
    }

    public function handle(Player $player, $response)
    {
        switch ($response) {
            case 0:
            case 1:
                Main::getInstance()->getWindows()->getByName('mailbox')->show($player);
                break;
        }
        $this->close();
    }
}