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
use pocketmine\level\biome\SnowyBiome;
use pocketmine\level\generator\populator\TallGrass;
use rivex\rivexcore\modules\generator\space\populator\IglooPopulator;

class IcePlains extends SnowyBiome implements Mountainable
{
    /**
     * Constructs the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->setGroundCover([
            Block::get(Block::SNOW, 0),
            Block::get(Block::GRASS, 0),
            Block::get(Block::DIRT, 0),
            Block::get(Block::DIRT, 0),
            Block::get(Block::DIRT, 0)
        ]);
        $this->addPopulator(new IglooPopulator());

        $tallGrass = new TallGrass();
        $tallGrass->setBaseAmount(3);

        $this->addPopulator($tallGrass);

        $this->setElevation(63, 74);

        $this->temperature = 0.05;
        $this->rainfall = 0.8;
    }

    /**
     * Returns the biome name
     *
     * @return string
     */
    public function getName(): string
    {
        return "BetterIcePlains";
    }

    /**
     * Returns the biomes' id.
     *
     * @return int biome id
     */
    public function getId(): int
    {
        return Biome::ICE_PLAINS;
    }
}