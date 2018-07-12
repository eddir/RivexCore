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

class Uranus extends Spherical implements Location
{
    const NAME = 'Уран';

    /** @var SpaceGenerator */
    private $generator;

    // голубой
    private $baseBlock = [Block::SNOW, 0];

    private $compoundBlocks = [
        [Block::CONCRETE, 3, 40, 50, 0, 100],
        [Block::TERRACOTTA, 3, 30, 50, 0, 100],
        [Block::CONCRETEPOWDER, 11, 20, 50, 0, 100],

        [Block::EMERALD_ORE, 0, 1, 4, 1, 40],
        [Block::REDSTONE_ORE, 0, 14, 22, 1, 60],
        [Block::LAPIS_ORE, 0, 2, 14, 1, 30]
    ];

    private $generationPopulators = [];
    private $populators = [];

    private $luquid = [Block::ICE, 0];

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
        return 'Uranus';
    }
}
