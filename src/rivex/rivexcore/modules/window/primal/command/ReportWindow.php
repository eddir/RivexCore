<?php

namespace rivex\rivexcore\modules\window\primal\command;

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
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\BaseWindow;
use rivex\rivexcore\modules\window\element\Dropdown;
use rivex\rivexcore\modules\window\element\Input;
use rivex\rivexcore\modules\window\element\Label;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;

class ReportWindow extends BaseWindow implements Window
{

    public $answer, $question, $admin, $parent;

    public function __construct($id)
    {
        $this->ui = new Custom("Связь с администрацией");
        parent::__construct($id, 'report');
    }

    public function choice()
    {
        // TODO: а не слишком ли это затратно?
        return Main::getInstance()->getWindows()->add(self::class);
    }

    public function prepare(Player $player)
    {
        $this->ui->clean();
        $reply = $this->answer and $this->question and $this->admin;
        if ($reply) {
            $this->ui->addElement(new Label("§7Вы спросили:§f\n\n" . $this->question . "\n\n§f§7Вам отвечает §f" . $this->admin . " §f:\n\n" . $this->answer . "\n\n§f§7Вы можете продолжить разговор или оценить ответ ниже."));
        } else {
            $this->ui->addElement(new Label("§7Администрация сервера ответит на Ваш вопрос в ближайщее время."));
        }
        if (!$reply and $this->question) {
            $this->ui->addElement(new Input("", "§fВаше сообщение", $this->question));
        } else {
            $this->ui->addElement(new Input("", "§fВаше сообщение"));
        }
        if ($reply) {
            $this->ui->addElement(new Dropdown(
                'Оценка ответу',
                array("Отлично", "Хорошо", "Приемлимо", "Плохо")
            ));
        }
        $this->serialize();
        return true;
    }

    public function handle(Player $player, $response)
    {
        $text = $response[1];
        if (strlen($text) > 3) {
            if (strlen($text) < 300) {
                //socket
                //TODO: make a function with sockets
                //TODO: put into Thread
                if ($this->parent) {
                    Main::getInstance()->getDbGlobal()->query('UPDATE `tickets` SET `rating` = #d, `isread` = 1 WHERE `id` = #d', $this->translateRate($response[2]['index']), $this->parent);
                }
                $request = json_encode(array('act' => 'report', 'body' => array('usr' => $player->getLowerCaseName(), 'msg' => $text)));
                if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
                    if (socket_connect($socket, '127.0.0.1', 19100)) {

                        socket_read($socket, 1024);
                        $password = "8hfUfb56Gdnj";
                        socket_write($socket, $password, strlen($password));
                        $read = socket_read($socket, 1024);
                        if (trim($read) == 'OK') {
                            socket_write($socket, $request . "\n", strlen($request) + 1);
                            socket_read($socket, 1024);
                            socket_close($socket);
                            $player->sendMessage('§aСообщение отправлено администраторам!');
                        } else {
                            $player->sendMessage('§eНе удалось отправить сообщение. Обратитесь к администрации.');
                        }
                    } else {
                        $player->sendMessage('Ошибка при соединении');
                    }
                } else {
                    $player->sendMessage('Ошибка при создании сокета');
                }


            } else {
                $player->sendMessage('§eСообщение слишком длинное');
            }
        } elseif ($this->parent) {
            Main::getInstance()->getDbGlobal()->query('UPDATE `tickets` SET `rating` = #d, `isread` = 1 WHERE `id` = #d', $this->translateRate($response[2]['index']), $this->parent);
            $player->sendMessage('§eБлагодарим за отзыв!');
        } else {
            $player->sendMessage('§eСообщение слишком короткое!');
        }
        $this->close();
    }

    private function translateRate($key)
    {
        $rate = array(
            0 => 5,
            1 => 4,
            2 => 3,
            3 => 2
        );
        return isset($rate[$key]) ? $rate[$key] : 5;
    }

}
