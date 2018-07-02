<?php
declare(strict_types=1);

namespace rivex\rivexcore\command;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use rivex\rivexcore\Main;

class AnswerReport extends RivexCommand
{
    /**
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        parent::__construct($main, "answerreport", "Send answer for user", "", false);
        $this->setPermission("rivex.command.answerreport");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $alias, array $args): bool
    {
        if ($sender instanceof Player) {
            $sender->sendMessage('НЛО прилетело сюда и оставило это сообщение....');
            return true;
        }
        if (count($args) == 2) {
            if (($user = $this->getMain()->getUser($args[0]))) {
                if ($user->sendAnswer($args[1])) {
                    $this->getMain()->getDbGlobal()->query('UPDATE `tickets` SET `isread` = 1 WHERE `id` = #d', $args[1]);
                    $sender->sendMessage('Success');
                } else {
                    $sender->sendMessage('Error: id');
                }
            } else {
                $sender->sendMessage('Error: offline');
            }
        } else {
            $sender->sendMessage('Error: undefined');
        }
        return true;
    }
}
