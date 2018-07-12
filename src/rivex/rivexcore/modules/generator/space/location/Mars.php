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

class Mars extends Spherical implements Location
{
    const NAME = 'Марс';

    /** @var SpaceGenerator */
    private $generator;

    // светло красный
    private $baseBlock = [Block::NETHERRACK, 0];

    private $compoundBlocks = [
        [Block::CONCRETEPOWDER, 14, 50, 50, 1, 100],
        [Block::SAND, 1, 20, 70, 1, 100],
        [Block::MAGMA, 0, 25, 30, 1, 100],
        [Block::TERRACOTTA, 14, 30, 20, 1, 100],

        [Block::DIAMOND_ORE, 0, 1, 4, 0, 25],
        [Block::GOLD_ORE, 0, 8, 5, 5, 80],
        [Block::COAL_ORE, 0, 15, 20, 1, 70]
    ];

    private $generationPopulators = [];
    private $populators = [];

    private $luquid = [Block::LAVA, 0];

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
    public static function getName(): string
    {
        return 'Mars';
    }
}
