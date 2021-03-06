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
use rivex\rivexcore\modules\window\element\Label;
use rivex\rivexcore\modules\window\type\Custom;
use rivex\rivexcore\modules\window\Window;

class TopFractionWindow extends BaseWindow implements Window
{

    public function __construct($id)
    {
        $this->ui = new Custom("Лучшие фракции");
        parent::__construct($id, 'topfraction');
    }

    public function choice()
    {
        return $this;
    }

    public function prepare(Player $player)
    {
        $this->ui->clean();
        $top = Main::getInstance()->getFractions()->getTop();
        if (isset($top[0])) {
            foreach ($top as $i => $fraction) {
                $text = "§f§7" . ($i + 1) . ". §a§l" . $fraction['name'] . "\n";
                if (!empty($fraction['description']))
                    $text .= "§f§e" . $fraction['description'] . "\n";

                $this->ui->addElement(new Label($text));
                //TODO: toggle
            }
        } else {
            $this->ui->addElement(new Label('Ни одна фракция ещё не создана'));
        }
        $this->serialize();
        return true;
    }

    public function handle(Player $player, $response)
    {
        Main::getInstance()->getWindows()->getByName('fraction')->show($player);
        return true;
    }

}
