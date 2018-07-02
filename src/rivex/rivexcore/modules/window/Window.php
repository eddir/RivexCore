<?php

namespace rivex\rivexcore\modules\window;

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

use rivex\rivexcore\modules\window\type\WindowType;

interface Window
{

    /**
     * Отправить окно игроку
     *
     * @param Player $player
     */
    public function show(Player $player);

    /**
     * TODO: handle
     * @param Player $player
     * @param $response
     * @return mixed
     */
    public function handle(Player $player, $response);

    public function getId(): int;

    public function getName(): string;

    public function getUI(): WindowType;

    public function getCallable();

    public function choice();

}
