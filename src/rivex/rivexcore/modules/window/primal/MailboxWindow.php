<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 7/4/18
 * Time: 4:48 PM
 */

namespace rivex\rivexcore\modules\window\primal;


use pocketmine\Player;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Button;
use rivex\rivexcore\modules\window\type\Menu;
use rivex\rivexcore\modules\window\Window;

class MailboxWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Menu("Ваш почтовый ящик");
        parent::__construct($id, "mailbox");
    }

    public function prepare(Player $player)
    {
        $this->sessions[$player->getName()] = array();
        $this->ui->clean();
        $user = Main::getInstance()->getUser($player->getName());
        $mails = $user->getMail();
        $parcels = $user->getParcels();
        if (count($mails) > 0 or count($parcels) > 0) {
            $this->ui->setTitle("Ваш почтовый ящик");
            foreach ($mails as $mail) {
                $this->ui->addButton(new Button("Сообщение от " . $mail['sender']));
                $this->sessions[$player->getName()][] = array('mail', $mail);
            }
            foreach ($parcels as $parcel) {
                $this->ui->addButton(new Button("Посылка от торговца: " . $parcel['description']));
                $this->sessions[$player->getName()][] = array('parcel', $parcel);
            }
        } else {
            $this->ui->addButton(new Button("Ящик пустой."));
        }
        $this->serialize();
        return true;
    }

    public function choice()
    {
        return $this;
    }

    public function handle(Player $player, $response)
    {
        if (isset($this->sessions[$player->getName()][$response])) {
            $item = $this->sessions[$player->getName()][$response];
            if ($item[0] == 'mail') {
                /** @var MailWindow $window */
                $window = Main::getInstance()->getWindows()->getByName('mail');
                $window->player = $item['player'];
                $window->sender = $item['sender'];
                $window->body = $item['body'];
                $window->show($player);
            } elseif ($item[0] == 'parcel') {
                /** @var ParcelWindow $window */
                $window = Main::getInstance()->getWindows()->getByName('parcel');
                $window->item_id = $item['1']['item_id'];
                $window->item_damage = $item['1']['item_damage'];
                $window->item_amount = $item['1']['item_amount'];
                $window->description = $item['1']['description'];
                $window->show($player);
            }
        }
    }
}