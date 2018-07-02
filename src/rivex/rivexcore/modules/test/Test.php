<?php

namespace rivex\rivexcore\modules\test;

use rivex\rivexcore\Main;

class Test
{

    private $main;
    private $test = "ok";

    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    public function getMain()
    {
        return $this->main;
    }

    public function test()
    {
        //$this->futureTest();
        //$this->educateTest();
		
		//$this->getMain()->getScheduler()->scheduleDelayedTask(new TestTask($this), 20 * 90);
    }

    private function educateTest()
    {
        echo 'Start educate test', PHP_EOL;
        for ($x = 0; $x < 16; ++$x) {
            echo $x, PHP_EOL;
        }
        echo 'End educate test', PHP_EOL;
    }

    private function futureTest()
    {
        var_dump("Begin 5");
        $this->getMain()->getWorkQueue()->addWork(
            function ($text) {
                var_dump("Work Queue: $text", $this->test);
            },
            5,
            ["5 seconds comes"]
        );
        return true;
    }

}
