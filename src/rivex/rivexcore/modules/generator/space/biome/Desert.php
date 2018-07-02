<?php
/**
 *  ____             __     __                    ____
 * /\  _`\          /\ \__ /\ \__                /\  _`\
 * \ \ \L\ \     __ \ \ ,_\\ \ ,_\     __   _ __ \ \ \L\_\     __     ___
 *  \ \  _ <'  /'__`\\ \ \/ \ \ \/   /'__`\/\`'__\\ \ \L_L   /'__`\ /' _ `\
 *   \ \ \L\ \/\  __/ \ \ \_ \ \ \_ /\  __/\ \ \/  \ \ \/, \/\  __/ /\ \/\ \
 *    \ \____/\ \____\ \ \__\ \ \__\\ \____\\ \_\   \ \____/\ \____\\ \_\ \_\
 *     \/___/  \/____/  \/__/  \/__/ \/____/ \/_/    \/___/  \/____/ \/_/\/_/
 * Tomorrow's pocketmine generator.
 * @author Ad5001 <mail@ad5001.eu>, XenialDan <https://github.com/thebigsmileXD>
 * @link https://github.com/Ad5001/BetterGen
 * @category World Generator
 * @api 3.0.0
 * @version 1.1
 */

namespace rivex\rivexcore\modules\generator\space\biome;

use pocketmine\block\Block;
use pocketmine\level\biome\Biome;
use pocketmine\level\biome\SandyBiome;
use rivex\rivexcore\modules\generator\space\populator\CactusPopulator;
use rivex\rivexcore\modules\generator\space\populator\DeadbushPopulator;
use rivex\rivexcore\modules\generator\space\populator\SugarCanePopulator;
use rivex\rivexcore\modules\generator\space\populator\TemplePopulator;
use rivex\rivexcore\modules\generator\space\populator\WellPopulator;

class Desert extends SandyBiome implements Mountainable
{
    /**
     * Constructs the class
     */
    public function __construct()
    {
        parent::__construct();
        $deadBush = new DeadbushPopulator ();
        $deadBush->setBaseAmount(1);
        $deadBush->setRandomAmount(2);

        $cactus = new CactusPopulator ();
        $cactus->setBaseAmount(1);
        $cactus->setRandomAmount(2);

        $sugarCane = new SugarCanePopulator ();
        $sugarCane->setRandomAmount(20);
        $sugarCane->setBaseAmount(3);

        $temple = new TemplePopulator ();

        $well = new WellPopulator ();

        $this->addPopulator($cactus);
        $this->addPopulator($deadBush);
        $this->addPopulator($sugarCane);
        $this->addPopulator($temple);
        $this->addPopulator($well);

        $this->setElevation(63, 70);
        // $this->setElevation(66, 70);

        $this->temperature = 0.5;
        $this->rainfall = 0;
        $this->setGroundCover([
            Block::get(Block::SAND, 0),
            Block::get(Block::SAND, 0),
            Block::get(Block::SAND, 0),
            Block::get(Block::SAND, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0)
        ]);
    }

    /**
     * Constructs the class
     *
     * @return string
     */
    public function getName(): string
    {
        return "BetterDesert";
    }

    /**
     * Returns biome id
     *
     * @return int
     */
    public function getId(): int
    {
        return Biome::DESERT;
    }
}
