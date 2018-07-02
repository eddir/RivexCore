<?php

namespace rivex\rivexcore\modules\window\primal\command\fraction;

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
use rivex\rivexcore\modules\window\element\Input;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;
use rivex\rivexcore\utils\exception\QueryErrorException;

class CreateFractionWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Создать фракцию");
        $this->ui->addElement(new Input("Название фракции", "От 3 до 32 символов без пробелов"));
        $this->ui->addElement(new Input("Краткое описание", "Не обязательно"));
        parent::__construct($id, 'createfraction');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        return true;
    }

    public function handle(Player $player, $response)
    {
        if (!$player->hasPermission('rivex.action.fraction.create')) {
            $player->sendMessage('§eУ вас нет прав на создание фракции. Купите привилегию, чтобы стать лидером фракции.');
            return true;
        }
        $user = Main::getInstance()->getUser($player->getName());
        if ($user->getRank() == 0) {
            if (strlen($response[0]) > 2 && strlen($response[0]) < 33) {
                if (strlen($response[1]) < 33) {
                    try {
                        if (!Main::getInstance()->getFractions()->exists($response[1])) {
                            $name = str_replace(" ", "", strtolower($response[0]));
                            Main::getInstance()->getFractions()->create($name, $user, $response[1]);
                            $player->sendMessage("§eФракция успешно создана!");
                        } else {
                            $player->sendMessage("§eТакая фракция уже существует. Придумайте другое название.");
                        }
                    } catch (QueryErrorException $e) {
                        $player->sendMessage("§eВы ввели некорректное название. Попробуйте другое. Если вы писали на русском языке, то дождитесь исправления или пишите на английском.");
                    }
                } else {
                    $player->sendMessage("§eВы ввели слишком длинное описание!");
                }
            } else {
                $player->sendMessage("§eНазвание клана должно быть от 2 до 32 символов!");
            }
        } else {
            $player->sendMessage('§eВы не можете создать клан: у вас уже есть фракция!');
        }
        return true;
    }

}
