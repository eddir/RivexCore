<?php

namespace rivex\rivexcore;
// коммент
/*
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


use pocketmine\math\Vector2;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\Server;
use rivex\DataBase\Connection;

use rivex\rivexcore\command\AnswerReport;
use rivex\rivexcore\command\DeleteHome;
use rivex\rivexcore\command\EntityKill;
use rivex\rivexcore\command\Fraction;
use rivex\rivexcore\command\Home;
use rivex\rivexcore\command\Menu;
use rivex\rivexcore\command\override\Give;
use rivex\rivexcore\command\override\Help;
use rivex\rivexcore\command\Report;
use rivex\rivexcore\command\Spawn;
use rivex\rivexcore\listener\CallbackListener;
use rivex\rivexcore\listener\EventListener;
use rivex\rivexcore\listener\WorldProtection;
use rivex\rivexcore\modules\fraction\FractionManager;
use rivex\rivexcore\modules\generator\Generator;
use rivex\rivexcore\modules\generator\task\TerritoryLimitTask;
use rivex\rivexcore\modules\test\Test;
use rivex\rivexcore\modules\window\WindowsManager;
use rivex\rivexcore\utils\exception\LogicException;
use rivex\rivexcore\utils\WorkQueue;

class Main extends PluginBase
{

    const TEST = true;

    const CONFIG_VERSION = 1;

    private static $instance;
    /** @var Connection */
    private $dbLocal, $dbGlobal;
    /** @var WindowsManager */
    private $windows;
    /** @var FractionManager */
    private $fractions;
    /** @var array */
    private $users = array();
    /** @var Test */
    private $test;
    /** @var WorkQueue */
    private $workQueue;
    /** @var CallbackListener */
    private $events;

    /**
     * @return Main
     */
    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function onEnable()
    {
        self::$instance = $this;
        if (self::TEST) {
            $this->getLogger()->warning('Установлен тестовый режим. Это может привести к непредсказуемым последствиям. Используйте с осторожностью!');
        }
        if (!$this->checkVersion()) {
            $this->crash('Устаревшая версия ядра PocketMine! Требуется PMMP с поддержкой Forms-api.');
        }

        if (!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        $this->saveDefaultConfig();

        if ($this->getConfig()->get('version', 0) < self::CONFIG_VERSION) {
            $this->getLogger()->warning('Устаревшая версия конфига. Удалите его для генерирования нового.');
        }
        /** @var \rivex\DataBase\Main $database */
        $database = $this->getServer()->getPluginManager()->getPlugin('DataBase');
        $this->dbLocal = $database->getLocal();
        $this->dbGlobal = $database->getGlobal();

        $this->dbGlobal->createTable("mail", array(
            "id" => "INT(6) NOT NULL AUTO_INCREMENT",
            "player" => "VARCHAR(32) NOT NULL",
            "sender" => "VARCHAR(32) NOT NULL",
            "body" => "TEXT"
        ));
        $this->dbGlobal->createTable("parcels", array(
            "id" => "MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT",
            "player" => "VARCHAR(32) NOT NULL",
            "item_id" => "SMALLINT UNSIGNED NOT NULL",
            "item_damage" => "TINYINT UNSIGNED NOT NULL",
            "item_amount" => "TINYINT UNSIGNED NOT NULL",
            "description" => "VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci",
            "server" => "SMALLINT NOT NULL"
        ));
        $this->dbLocal->createTable("homes", array(
            "id" => "MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT",
            "player" => "VARCHAR(32) NOT NULL",
            "name" => "VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci",
            "x" => "SMALLINT NOT NULL",
            "y" => "SMALLINT NOT NULL",
            "z" => "SMALLINT NOT NULL",
        ));

        $this->fractions = new FractionManager($this);
        $this->windows = new WindowsManager($this);
        $this->events = new CallbackListener($this);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents($this->windows, $this);
        $this->getServer()->getPluginManager()->registerEvents($this->events, $this);

        $this->workQueue = new WorkQueue($this);
        $this->workQueue->init();

        $this->registerCommands();

        $world_limit = $this->getConfig()->get('world-limit', array('radius' => 0));
        if ($world_limit['radius'] > 0) {
            $this->getScheduler()->scheduleRepeatingTask(new TerritoryLimitTask(
                new Vector2($world_limit['x'], $world_limit['z']), $world_limit['radius']
            ), 60);
        }

        if ($this->getConfig()->get('protect-world', false)) {
            $this->getServer()->getPluginManager()->registerEvents(new WorldProtection($this), $this);
        }

        new Generator($this);

        $this->test = new Test($this);
        $this->test->test();
    }

    /**
     * Может быть использовано для проверки существования
     * нужных модулей.
     *
     * @return bool
     */
    public function checkVersion()
    {
        return true;
    }

    /**
     * @param string $message
     */
    public function crash($message)
    {
        $this->getLogger()->error($message);
        if (!self::TEST)
            $this->finish();
        else
            $this->getLogger()->warning('Тестовый режим - остановка предотвращена');
    }

    public function finish()
    {
        $this->getServer()->shutdown();
    }

    //TODO: завершение при автоматическом перезапуске

    private function registerCommands(): void
    {
        $commands = [
            new Fraction($this),
            new Give($this),
            new Report($this),
            new AnswerReport($this),
            new Help($this),
            new Menu($this),
            new EntityKill($this),
            new Spawn($this),
            new Home($this),
            new DeleteHome($this)
        ];

        $aliased = [];
        foreach ($commands as $cmd) {
            $commands[$cmd->getName()] = $cmd;
            $aliased[$cmd->getName()] = $cmd->getName();
            foreach ($cmd->getAliases() as $alias) {
                $aliased[$alias] = $cmd->getName();
            }
        }
        $this->getServer()->getCommandMap()->registerAll("rivexcore", $commands);
    }

    public function onDisable()
    {
        ;
    }

    public static function getServerName(): string
    {
        $port = Server::getInstance()->getPort();
        switch ($port) {
            case 19132:
            case 19134:
                return "Хаб";
                break;
            case 19131:
            case 19133:
                return "Выживание в космосе";
                break;
        }
        return "Чёрная дыра";
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param $name
     * @return User
     */
    public function getUser(string $name): User
    {
        $name = strtolower($name);
        return isset($this->users[$name]) ? $this->users[$name] : null;
    }

    public function addUser(Player $player)
    {
        if (isset($this->users[$player->getLowerCaseName()])) {
            throw new LogicException("Player " . $player->getLowerCaseName() . " is already registered.");
        }
        $this->users[$player->getLowerCaseName()] = new User($player, $this);
    }

    public function RemoveUser(Player $player)
    {
        if (isset($this->users[$player->getLowerCaseName()])) {
            unset($this->users[$player->getLowerCaseName()]);
        } else {
            throw new LogicException("Player " . $player->getLowerCaseName() . " is not valid.");//TODO!!! zzzz
        }
    }

    /**
     * @return WindowsManager
     */
    public function getWindows()
    {
        return $this->windows;
    }

    /**
     * @return FractionManager
     */
    public function getFractions(): FractionManager
    {
        return $this->fractions;
    }

    /**
     * @return Connection
     */
    public function getDbLocal(): Connection
    {
        return $this->dbLocal;
    }

    /**
     * @return Connection
     */
    public function getDbGlobal(): Connection
    {
        return $this->dbGlobal;
    }

    /**
     * @return WorkQueue
     */
    public function getWorkQueue(): WorkQueue
    {
        return $this->workQueue;
    }

    /**
     * @return CallbackListener
     */
    public function getEvents(): CallbackListener
    {
        return $this->events;
    }
}
