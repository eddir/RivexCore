<?php
declare(strict_types=1);

namespace rivex\rivexcore\command;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use rivex\rivexcore\Main;
use rivex\rivexcore\modules\window\primal\RegisterWindow;

class Report extends RivexCommand
{
    /**
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        parent::__construct($main, "report", "Send nessage for admin", "", false, ['re']);
        $this->setPermission("rivex.command.report");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $alias, array $args): bool
    {
        if (!$this->testPermission($sender)) {
            return false;
        }
        if ($sender instanceof Player) {
            $sender->sendMessage("Временно не работает");
            return true;
            // TODO: убрать declare dynamicly и сделать другое сохранение вопроса
            /** @var RegisterWindow $window */
            $window = $this->getMain()->getWindows()->getByName('report');
            if (count($args) > 0) {
                $window->question = implode(' ', $args);
            }
            $window->show($sender);
        } else {
            //ONLY FOR DEBUG
            $text = implode(' ', $args);
            $request = json_encode(array('act' => 'report', 'body' => array('usr' => $sender->getName(), 'msg' => $text)));
            if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
                if (socket_connect($socket, '127.0.0.1', 19100)) {
                    socket_read($socket, 1024);
                    $password = "8hfUfb56Gdnj";
                    socket_write($socket, $password, strlen($password));
                    $read = socket_read($socket, 1024);
                    if (trim($read) == 'OK') {
                        socket_write($socket, $request . "\n", strlen($request) + 1);
                        socket_read($socket, 1024);
                    }
                    socket_close($socket);
                } else {
                    $sender->sendMessage('Ошибка при соединении');
                }
            } else {
                $sender->sendMessage('Ошибка при создании сокета');
            }

        }

        return true;
    }
}
