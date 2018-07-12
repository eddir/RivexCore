<?php

namespace rivex\rivexcore\modules\generator\space\location;

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

use pocketmine\block\Block;
use pocketmine\level\generator\Generator;
use pocketmine\utils\Random;
use rivex\rivexcore\modules\generator\space\object\OreType;
use rivex\rivexcore\modules\generator\space\populator\Ore;
use rivex\rivexcore\modules\generator\space\SpaceGenerator;

class Saturn extends Spherical implements Location
{
    const NAME = 'Сатурн';

    /** @var SpaceGenerator */
    private $generator;

    // желто-белый
    private $baseBlock = [Block::SAND, 0];

    private $compoundBlocks = [
        [Block::CONCRETEPOWDER, 0, 50,  50, 1, 100],
        [Block::CONCRETE,       0, 50,  50, 1, 100],
        [Block::QUARTZ_ORE, 0, 30, 50, 1, 100],
        [Block::BONE_BLOCK, 0, 40, 10, 1, 100],

        [Block::COAL_ORE, 0, 15, 35, 1, 70],
        [Block::EMERALD_ORE, 0, 3, 5, 1, 40],
        [Block::REDSTONE_ORE, 0, 8, 17, 1, 60]
    ];

    private $generationPopulators = [];
    private $populators = [];

    private $luquid = [Block::AIR, 0];

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;

        $types = [];
        foreach ($this->compoundBlocks as $type) {
            $types[] = new OreType(Block::get($type[0], $type[1]), $type[2], $type[3], $type[4], $type[5]);
        }
        $ores = new Ore();
        $ores->setOreTypes($types);
        $ores->setReplacement($this->baseBlock[0]);
        $this->populators[] = $ores;
    }

    public function generateChunk(int $chunkX, int $chunkZ, Random $random): void
    {
        $noise = $this->generator->getNoiseBase()->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);
        $chunk = $this->generator->getLevel()->getChunk($chunkX, $chunkZ);
        parent::generateSphere($chunk, $noise, $this->baseBlock, $this->luquid, $random);
    }

    public function generationPopulateChunk($chunkX, $chunkZ, $random)
    {
        $chunk = $this->generator->getLevel()->getChunk($chunkX, $chunkZ);
        parent::populateSphere($chunk, $this->baseBlock, $this->luquid, $random);

        foreach ($this->generationPopulators as $populator) {
            $populator->populate($this->generator->getLevel(), $chunkX, $chunkZ, $random);
        }
    }

    public function populateChunk(int $chunkX, int $chunkZ, Random $random): void
    {
        foreach ($this->populators as $populator) {
            $populator->populate($this->generator->getLevel(), $chunkX, $chunkZ, $random);
        }
    }

    public function getGenerator(): Generator
    {
        return $this->generator;
    }

    /**
     * @return string
     */
    public static function getName()
    {
        return 'Saturn';
    }
}
