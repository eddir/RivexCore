<?php

namespace rivex\rivexcore\modules\test;

use pocketmine\math\Vector3;

use pocketmine\scheduler\Task;

/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 15.06.18
 * Time: 22:36
 */
class TestTask extends Task
{
	
	private $test;

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        $this->pasteSpawnMap();
    }
	
	public function __construct(Test $test)
	{
		$this->test = $test;
	}
	
	private function pasteSpawnMap()
    {
		$t1 = microtime(true);
		$server = $this->getMain()->getServer();
    	$level = $server->getDefaultLevel();
		echo 'Очищаю', PHP_EOL;
		/*
    	for ($x = 0; $x <= 1024; $x++) {
			for ($y = 0; $y <= 130; $y++) {
    			for ($z = 0; $z <= 1024; $z++) {
					echo $x, ' ', $y, ' ', $z, PHP_EOL;
    				$level->setBlock(new Vector3($x, $y, $z), Block::get(0));
				}
    		}
    	}
		*/
		$t2 = microtime(true);
		echo 'Готово (', $t2 - $t1, ')', PHP_EOL;
		echo 'Загружаю', PHP_EOL;
		if (!$server->loadLevel('spawn')) {
			echo 'Не получилось :(', PHP_EOL;
			return;
		}
		
		$spawn = $server->getLevelByName('spawn');
		$t3 = microtime(true);
		echo 'Готово (', $t3 - $t2, ')', PHP_EOL;
		
		echo 'Загружаю', PHP_EOL;
		
		for ($x = 388; $x <= 682; $x += 16) {
			for ($z = 82; $z <= 206; $z += 16) {
				$spawn->loadChunk(floor($x / 16), floor($z / 16));
				$level->loadChunk(floor($x / 16), floor($z / 16));
			}
		}
		
		echo 'Вставляю', PHP_EOL;
		
		for ($x = 388; $x <= 682; $x++) {
			for ($z = 230; $z <= 506; $z++) {
				for ($y = 82; $y <= 206; $y++) {
					echo $x, ' ', $y, ' ', $z, PHP_EOL;
					$level->setBlock(new Vector3($x - 388, $y, $z - 230), $spawn->getBlock(new Vector3($x, $y, $z)));
				}
			}
		}
		
		echo 'Done (', microtime(true) - $t1, ')', PHP_EOL;
    }
	
	public function getMain()
	{
		return $this->test->getMain();
	}
}
