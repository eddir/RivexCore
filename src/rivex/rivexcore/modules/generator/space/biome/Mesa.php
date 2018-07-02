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
use pocketmine\block\GoldOre;
use pocketmine\level\biome\SandyBiome;
use rivex\rivexcore\modules\generator\space\populator\CactusPopulator;
use rivex\rivexcore\modules\generator\space\populator\DeadbushPopulator;
use rivex\rivexcore\modules\generator\space\populator\SugarCanePopulator;
use rivex\rivexcore\modules\generator\space\populator\TreePopulator;

class Mesa extends SandyBiome
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

        $sugarCane = new TreePopulator ();
        $sugarCane->setRandomAmount(2);
        $sugarCane->setBaseAmount(0);

        $ores = new \pocketmine\level\generator\populator\Ore();
        $ores->setOreTypes([
            new \pocketmine\level\generator\object\OreType(new GoldOre (), 2, 8, 0, 32)
        ]);

        $this->addPopulator($cactus);
        $this->addPopulator($deadBush);
        $this->addPopulator($sugarCane);
        $this->addPopulator($ores);

        $this->setElevation(80, 83);
        // $this->setElevation(66, 70);

        $this->temperature = 0.8;
        $this->rainfall = 0;
        $this->setGroundCover([
            Block::get(Block::DIRT, 0),
            Block::get(Block::HARDENED_CLAY, 0),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::HARDENED_CLAY, 0),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 7),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::HARDENED_CLAY, 0),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 12),
            Block::get(Block::STAINED_HARDENED_CLAY, 12),
            Block::get(Block::STAINED_HARDENED_CLAY, 12),
            Block::get(Block::STAINED_HARDENED_CLAY, 14),
            Block::get(Block::STAINED_HARDENED_CLAY, 14),
            Block::get(Block::STAINED_HARDENED_CLAY, 14),
            Block::get(Block::STAINED_HARDENED_CLAY, 4),
            Block::get(Block::STAINED_HARDENED_CLAY, 7),
            Block::get(Block::STAINED_HARDENED_CLAY, 0),
            Block::get(Block::STAINED_HARDENED_CLAY, 7),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::HARDENED_CLAY, 0),
            Block::get(Block::HARDENED_CLAY, 0),
            Block::get(Block::HARDENED_CLAY, 0),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::STAINED_HARDENED_CLAY, 1),
            Block::get(Block::RED_SANDSTONE, 0),
            Block::get(Block::RED_SANDSTONE, 0),
            Block::get(Block::RED_SANDSTONE, 0),
            Block::get(Block::RED_SANDSTONE, 0),
            Block::get(Block::RED_SANDSTONE, 0),
            Block::get(Block::RED_SANDSTONE, 0),
            Block::get(Block::RED_SANDSTONE, 0),
            Block::get(Block::RED_SANDSTONE, 0)
        ]);
    }

    /**
     * Returns the biome name
     *
     * @return string
     */
    public function getName(): string
    {
        return "BetterMesa";
    }

    /**
     * Returns biome id
     *
     * @return int
     */
    public function getId(): int
    {
        return 39;
    }
}
