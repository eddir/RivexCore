<?php

namespace rivex\rivexcore;

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

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector2;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use rivex\DataBase\Connection;

use rivex\rivexcore\command\AnswerReport;
use rivex\rivexcore\command\Fraction;
use rivex\rivexcore\command\Menu;
use rivex\rivexcore\command\override\Give;
use rivex\rivexcore\command\override\Help;
use rivex\rivexcore\command\Report;
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
    private $fractions;
    private $users = array();
    private $test;
    private $workQueue;

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

        $database = $this->getServer()->getPluginManager()->getPlugin('Database');
        /** @var $database \rivex\DataBase\Main */
        if ($database) {
            $this->dbLocal = $database->getLocal();
            $this->dbGlobal = $database->getGlobal();
            $this->fractions = new FractionManager($this);
        } else {
            $this->crash('Плагин DataBase не найден. Аварийное завершение!');
        }

        $this->windows = new WindowsManager($this);
        $this->registerCommands();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents($this->windows, $this);

        $this->workQueue = new WorkQueue($this);
        $this->workQueue->init();
		
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
            new Menu($this)
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

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param string[] $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case "examplecommand":
                $sender->sendMessage("Example command output");
                return true;
            default:
                return false;
        }
    }

    public function onDisable()
    {
        ;
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
    public function getUser($name): User
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

    public function getDbLocal()
    {
        return $this->dbLocal;
    }

    public function getDbGlobal()
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
}
