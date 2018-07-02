<?php

namespace rivex\rivexcore;

use pocketmine\Player;
use rivex\rivexcore\modules\window\primal\command\ReportWindow;


class User
{

    private $main;

    private $player;

    private $fraction;

    private $kills = 0;
    private $deaths = 0;
    private $rank = 0;

    //TODO or not to-do?
    private $lastlogin, $firstlogin;

    public function __construct(Player $player, Main $main)
    {
        $this->player = $player;
        $this->main = $main;
        if ($this->getMain()->getDbLocal()->exists('SELECT * FROM `users` WHERE `users`.`name` = #s', $this->getPlayer()->getLowerCaseName())) {
            $description = $this->getMain()->getDbLocal()->fetch_one('SELECT * FROM `users` WHERE `name` = #s', $this->getPlayer()->getLowerCaseName());
            $this->fraction = $description['fraction'];
            $this->deaths = $description['deaths'];
            $this->kills = $description['kills'];
            $this->rank = $description['rank'];
        } else {
            $this->getMain()->getDbLocal()->query('INSERT INTO `users` (`name`) VALUES(#s)', $this->getPlayer()->getLowerCaseName());
        }
    }

    public function getFraction()
    {
        return $this->fraction;
    }

    public function setFraction($fraction, $rank = 0, $update = true)
    {
        if ($update)
            $this->getMain()->getDbLocal()->query("UPDATE `users` SET `fraction` = #s, `rank` = #d WHERE `name` = #s", $fraction, $rank, $this->getPlayer()->getLowerCaseName());
        $this->fraction = $fraction;
        $this->rank = $rank;
    }

    public function leaveFraction()
    {
        $this->getMain()->getDbLocal()->query('UPDATE `users` SET `fraction` = null, `rank` = 0 WHERE `name` = #s', $this->getPlayer()->getLowerCaseName());
        $this->fraction = null;
        $this->rank = 0;
    }

    public function getRank()
    {
        return $this->rank;
    }

    public static function getStringRank($rank = null)
    {
        switch ($rank) {
            case 0:
                return 'Одиночка';
            case 1:
                return 'Лидер';
            case 2:
                return 'Заместитель';
            case 3:
            default:
                return 'Участник';
        }
    }

    public function getDeaths()
    {
        return $this->deaths;
    }

    public function addDeath()
    {
        $this->getMain()->getDbLocal()->query('UPDATE `users` SET `deaths` = #d WHERE `name` = #s', ++$this->deaths, $this->getPlayer()->getLowerCaseName());
    }

    public function getKills()
    {
        return $this->kills;
    }

    public function addKill()
    {
        $this->getMain()->getDbLocal()->query('UPDATE `users` SET `kills` = #d WHERE `name` = #s', ++$this->kills, $this->getPlayer()->getLowerCaseName());
    }

    public function getMain()
    {
        return $this->main;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function sendAnswer($id)
    {
        /** @var ReportWindow $window */
        $window = $this->getMain()->getWindows()->getByName('report');
        $answer = $this->getMain()->getDbGlobal()->fetch_one('SELECT * FROM `tickets` WHERE `id` = #d', $id);
        if (isset($answer['text'])) {
            $question = $this->getMain()->getDbGlobal()->fetch_one('SELECT * FROM `tickets` WHERE `id` = #d', $answer['parent']);
            if (isset($question['text'])) {
                $admin = $this->getMain()->getDbGlobal()->fetch_one('SELECT * FROM `staff` WHERE `telegram` = #d', $answer['user']);
                if (isset($admin['name'])) {
                    $window->parent = $answer['id'];
                    $window->admin = $admin['name'];
                    $window->answer = $answer['text'];
                    $window->question = $question['text'];
                    $window->show($this->getPlayer());

                    return true;
                } else {
                    var_dump(3);
                }
            } else {
                var_dump(2);
            }
        } else {
            var_dump(1);
        }
        return false;
    }

}
