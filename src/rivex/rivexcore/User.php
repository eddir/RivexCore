<?php

namespace rivex\rivexcore;

use pocketmine\math\Vector3;
use pocketmine\Player;

use rivex\rivexcore\modules\window\primal\command\ReportWindow;


class User
{
    /** @var Main */
    private $main;
    /** @var Player */
    private $player;
    /** @var string */
    private $fraction;
    /** @var int */
    private $kills = 0;
    /** @var int */
    private $deaths = 0;
    /** @var int */
    private $rank = 0;

    //TODO or not to-do?
    private $lastlogin, $firstlogin;

    /**
     * User constructor.
     * @param Player $player
     * @param Main $main
     */
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

    public function getHomes(): array
    {
        return $this->getMain()->getDbLocal()->fetch_array('SELECT * FROM homes WHERE player = #s', $this->player->getName());
    }

    public function getHomeCount(): int
    {
        return $this->getMain()->getDbLocal()->num_rows('SELECT id FROM homes WHERE player = #s', $this->player->getName());
    }

    public function setHome(string $name, Vector3 $position)
    {
        $this->getMain()->getDbLocal()->query('INSERT INTO homes (player, name, x, y, z) VALUES (#s, #s, #d, #d, #d)',
            $this->getPlayer()->getName(), $name, $position->x, $position->y, $position->z);
    }

    public function removeHome(int $id)
    {
        $this->getMain()->getDbLocal()->query('DELETE FROM homes WHERE id = #d', $id);
    }

    /**
     * @return string
     */
    public function getFraction(): string
    {
        return $this->fraction;
    }

    /**
     * @param $fraction
     * @param int $rank
     * @param bool $update
     */
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

    /**
     * @param int|null $rank
     * @return string
     */
    public static function getStringRank($rank = null): string
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

    /**
     * @return array
     */
    public function getParcels(): array
    {
        return $this->getMain()->getDbGlobal()->fetch_array("SELECT * FROM parcels WHERE player = #s AND server = #s", $this->player->getName(), $this->getMain()->getServer()->getPort());
    }

    /**
     * @return array
     */
    public function getMail(): array
    {
        return $this->getMain()->getDbGlobal()->fetch_array("SELECT * FROM  mail WHERE player = #s", $this->player->getName());
    }

    /**
     * @return int
     */
    public function getDeaths(): int
    {
        return $this->deaths;
    }

    public function addDeath()
    {
        $this->getMain()->getDbLocal()->query('UPDATE `users` SET `deaths` = #d WHERE `name` = #s', ++$this->deaths, $this->getPlayer()->getLowerCaseName());
    }

    /**
     * @return int
     */
    public function getKills(): int
    {
        return $this->kills;
    }

    public function addKill()
    {
        $this->getMain()->getDbLocal()->query('UPDATE `users` SET `kills` = #d WHERE `name` = #s', ++$this->kills, $this->getPlayer()->getLowerCaseName());
    }

    /**
     * @return Main
     */
    public function getMain(): Main
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
