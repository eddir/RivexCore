<?php
declare(strict_types=1);

namespace rivex\rivexcore\command;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use rivex\rivexcore\Main;

class Fraction extends RivexCommand
{
    /**
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        parent::__construct($main, "fraction", "Open fraction menu", "", false, ["faction", "fraction", "clan"]);
        $this->setPermission("rivex.command.fraction");
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
            if (!isset($args[0])) {
                $this->getMain()->getWindows()->getByName('fraction')->show($sender);
            } else {
                switch ($args[0]) {
                    case 'generator':
                        $sender->sendMessage('Этой команды нет в данной версии RivexCore');
                        break;
                    case 'create':
                    case 'new':
                    case 'open':
                        if ($sender->hasPermission('rivex.action.fraction.create')) {
                            if (isset($args[1])) {
                                if (strlen($args[1]) > 2 && strlen($args[1]) < 16) {
                                    if (!$this->getMain()->getDbGlobal()->exists("SELECT `id` FROM `fractions` WHERE `name` = '" . strtolower($args[1]) . "'")) {
                                        $this->getMain()->getFractions()->create($args[1], $this->getMain()->getUser($sender->getName()));

                                    } else {
                                        $sender->sendMessage('§eФракция с таким именем уже существует!');
                                    }
                                } else {
                                    $sender->sendMessage('§eНазвание клана должно быть от 3 до 16 символов!');
                                }
                            } else {
                                $sender->sendMessage('§eНе указано название клана!');
                            }
                        } else {
                            $sender->sendMessage('§eУ вас нет прав на создание фракции. Купите привилегию, чтобы стать лидером фракции.');
                        }
                        break;
                    default:
                        $this->getMain()->getWindows()->getByName('fraction')->show($sender);
                        break;
                }
            }
        } else {
            $sender->sendMessage('Извините, но мы ещё не разработали эту команду для консоли');
        }
        return true;
    }
}
