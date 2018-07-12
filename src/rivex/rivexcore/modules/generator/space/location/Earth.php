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
 * UPDATED
 *
 * January 2018
 */

use pocketmine\block\Block;
use pocketmine\block\CoalOre;

use pocketmine\block\Dirt;

use pocketmine\block\Gravel;
use pocketmine\block\IronOre;
use pocketmine\block\LapisOre;
use pocketmine\block\RedstoneOre;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\object\OreType;
use pocketmine\level\generator\populator\GroundCover;
use pocketmine\level\generator\populator\Ore;
use pocketmine\level\generator\populator\Populator;
use pocketmine\level\Level;
use pocketmine\utils\Random;
use rivex\rivexcore\modules\generator\space\biome\BetterBiomeSelector;
use rivex\rivexcore\modules\generator\space\biome\Desert;
use rivex\rivexcore\modules\generator\space\biome\Forest;
use rivex\rivexcore\modules\generator\space\biome\IcePlains;
use rivex\rivexcore\modules\generator\space\biome\Mesa;
use rivex\rivexcore\modules\generator\space\biome\MesaPlains;
use rivex\rivexcore\modules\generator\space\biome\Mountainable;
use rivex\rivexcore\modules\generator\space\biome\River;
use rivex\rivexcore\modules\generator\space\populator\CavePopulator;
use rivex\rivexcore\modules\generator\space\populator\DungeonPopulator;
use rivex\rivexcore\modules\generator\space\populator\FloatingIslandPopulator;
use rivex\rivexcore\modules\generator\space\populator\LakePopulator;
use rivex\rivexcore\modules\generator\space\populator\MineshaftPopulator;
use rivex\rivexcore\modules\generator\space\populator\RavinePopulator;
use rivex\rivexcore\modules\generator\space\SpaceGenerator;

class Earth extends Spherical implements Location
{
    const NAME = 'Земля';

    private static $biomes;
    /** @var Biome */
    public static $biomeById;
    /** @var SpaceGenerator */
    private $generator;

    private $baseBlock = [Block::STONE, 0];

    private $compoundBlocks = [
        [Block::CONCRETEPOWDER, 4, 10000],
    ];

    private $luquid = [Block::STILL_WATER, 0];

    const WATER_HEIGHT = 63;

    private $selector;
    private $generationPopulators;
    private $populators;

    /**
     * Earth constructor.
     * @param SpaceGenerator $generator
     */
    public function __construct(SpaceGenerator $generator)
    {
        $this->generator = $generator;

        $this->registerBiome(Biome::getBiome(Biome::OCEAN));
        $this->registerBiome(Biome::getBiome(Biome::PLAINS));
        $this->registerBiome(new Desert ());
        $this->registerBiome(new Mesa ());
        $this->registerBiome(new MesaPlains ());
        $this->registerBiome(Biome::getBiome(Biome::TAIGA));
        $this->registerBiome(Biome::getBiome(Biome::SWAMP));
        $this->registerBiome(new River ());
        $this->registerBiome(new IcePlains ());
        $this->registerBiome(new Forest(0, [
            0.6,
            0.5
        ]));
        $this->registerBiome(new Forest(1, [
            0.7,
            0.8
        ]));

        $this->selector = new BetterBiomeSelector($generator->getRandom(), [
            self::class,
            "getBiome"
        ], self::getBiome(0, 0));

        foreach (self::$biomes as $rain) {
            foreach ($rain as $biome) {
                $this->selector->addBiome($biome);
            }
        }

        $this->selector->recalculate();

        // TODO: генерируется в том числе и в пустоте за пределами планет
        $cover = new GroundCover();
        $this->generationPopulators[] = $cover;

        $lake = new LakePopulator();
        $lake->setBaseAmount(0);
        $lake->setRandomAmount(1);
        $this->generationPopulators[] = $lake;


        $cave = new CavePopulator ();
        $cave->setBaseAmount(0);
        $cave->setRandomAmount(2);
        $this->generationPopulators[] = $cave;


        $ravine = new RavinePopulator ();
        $ravine->setBaseAmount(0);
        $ravine->setRandomAmount(51);
        $this->generationPopulators[] = $ravine;


        $mineshaft = new MineshaftPopulator ();
        $mineshaft->setBaseAmount(0);
        $mineshaft->setRandomAmount(102);
        $this->populators[] = $mineshaft;


        $fisl = new FloatingIslandPopulator();
        $fisl->setBaseAmount(0);
        $fisl->setRandomAmount(132);
        $this->populators[] = $fisl;


        $dungeon = new DungeonPopulator();
        $dungeon->setBaseAmount(0);
        $dungeon->setRandomAmount(20);
        $this->populators[] = $dungeon;


        $ores = new Ore();
        $ores->setOreTypes([
            new OreType(new CoalOre (), 20, 16, 0, 128),
            new OreType(new IronOre (), 20, 8, 0, 64),
            new OreType(new RedstoneOre (), 8, 7, 0, 16),
            new OreType(new LapisOre (), 1, 6, 0, 32),
            new OreType(new Dirt (), 20, 32, 0, 128),
            new OreType(new Gravel (), 10, 16, 0, 128)
        ]);
        $this->populators[] = $ores;
    }

    public static function registerBiome(Biome $biome): bool
    {
        if (!isset(self::$biomes[(string)$biome->getRainfall()])) self::$biomes[( string)$biome->getRainfall()] = [];
        self::$biomes[( string)$biome->getRainfall()] [( string)$biome->getTemperature()] = $biome;
        ksort(self::$biomes[( string)$biome->getRainfall()]);
        ksort(self::$biomes);
        self::$biomeById[$biome->getId()] = $biome;
        return true;
    }

    public static function getBiome($temperature, $rainfall)
    {
        $ret = null;
        if (!isset(self::$biomes[( string)round($rainfall, 1)])) {
            while (!isset(self::$biomes[( string)round($rainfall, 1)])) {
                if (abs($rainfall - round($rainfall, 1)) >= 0.05)
                    $rainfall += 0.1;
                if (abs($rainfall - round($rainfall, 1)) < 0.05)
                    $rainfall -= 0.1;
                if (round($rainfall, 1) < 0)
                    $rainfall = 0;
                if (round($rainfall, 1) >= 0.9)
                    $rainfall = 0.9;
            }
        }
        $b = self::$biomes[( string)round($rainfall, 1)];
        foreach ($b as $t => $biome) {
            if ($temperature <= (float)$t) {
                $ret = $biome;
                break;
            }
        }
        if (is_string($ret)) {
            $ret = new $ret ();
        }
        return $ret;
    }

    /**
     * @param int $id
     * @return Biome
     */
    public static function getBiomeById(int $id): Biome
    {
        return self::$biomeById[$id] ?? null;
    }

    public function pickBiome($x, $z)
    {
        $hash = $x * 2345803 ^ $z * 9236449 ^ $this->generator->getLevel()->getSeed();
        $hash *= $hash + 223;
        $xNoise = $hash >> 20 & 3;
        $zNoise = $hash >> 22 & 3;
        if ($xNoise == 3) {
            $xNoise = 1;
        }
        if ($zNoise == 3) {
            $zNoise = 1;
        }

        $b = $this->selector->pickBiome($x + $xNoise - 1, $z + $zNoise - 1);
        if ($b instanceof Mountainable && $this->generator->getRandom()->nextBoundedInt(1000) < 3) {
            $b = clone $b;
            // $b->setElevation($b->getMinElevation () + (50 * $b->getMinElevation () / 100), $b->getMaxElevation () + (50 * $b->getMinElevation () / 100));
        }
        return $b;
    }


    /**
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     */
    public function generateChunk(int $chunkX, int $chunkZ, Random $random): void
    {
        $noise = $this->generator->getNoiseBase()->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);
        $chunk = $this->generator->getLevel()->getChunk($chunkX, $chunkZ);
        $biomeCache = [];

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {

                $biome = $this->pickBiome($chunkX * 16 + $x, $chunkZ * 16 + $z);
                $chunk->setBiomeId($x, $z, $biome->getId());

                $remote = $this->getRemoteness($x + $chunk->getX() * 16, $z + $chunk->getZ() * 16);
                if ($remote < Spherical::DOWN) {
                    if ($remote < Spherical::FLAT) {
                        $maxY = Spherical::FLAT_HEIGHT;
                    } else {
                        $maxY = Spherical::SMOOTHING * sqrt(Spherical::DOWN - $remote) + $random->nextBoundedInt(2);
                    }
                } else {
                    $maxY = 0;
                }

                $minSum = 0;
                $maxSum = 0;
                $weightSum = 0;
                $smoothSize = SpaceGenerator::getSmoothSize();
                for ($sx = -$smoothSize; $sx <= $smoothSize; ++$sx) {
                    for ($sz = -$smoothSize; $sz <= $smoothSize; ++$sz) {
                        $weight = SpaceGenerator::getKernel()[$sx + $smoothSize][$sz + $smoothSize];

                        if ($sx === 0 and $sz === 0) {
                            $adjacent = $biome;
                        } else {
                            $index = Level::chunkHash($chunkX * 16 + $x + $sx, $chunkZ * 16 + $z + $sz);
                            if (isset($biomeCache[$index])) {
                                $adjacent = $biomeCache[$index];
                            } else {
                                $biomeCache[$index] = $adjacent = $this->pickBiome($chunkX * 16 + $x + $sx, $chunkZ * 16 + $z + $sz);
                            }
                        }

                        $minSum += ($adjacent->getMinElevation() - 1) * $weight;
                        $maxSum += $adjacent->getMaxElevation() * $weight;
                        $weightSum += $weight;
                    }
                }
                $minSum /= $weightSum;
                $maxSum /= $weightSum;
                $smoothHeight = ($maxSum - $minSum) / 2;

                for ($y = 0; $y < $maxY; $y++) {
                    if ($y < 3 || ($y < 5 && $random->nextBoolean ())) {
                        $chunk->setBlockId($x, $y, $z, Block::BEDROCK);
                        continue;
                    }
                    $noiseValue = $noise[$x][$z][$y] - 1 / $smoothHeight * ($y - $smoothHeight - $minSum);
                    if ($noiseValue > 0) {
                        $chunk->setBlock($x, $y, $z, $this->baseBlock[0], $this->baseBlock[1]);
                    } elseif ($y <= self::WATER_HEIGHT) {
                        $chunk->setBlock($x, $y, $z, $this->luquid[0], $this->luquid[1]);
                    }
                }
            }
        }
        foreach ($this->generationPopulators as $populator) {
            /** @var $populator Populator */
            $populator->populate($this->generator->getLevel(), $chunkX, $chunkZ, $this->generator->getRandom());
        }
    }

    public function populateChunk(int $chunkX, int $chunkZ, Random $random): void
    {
        if (Spherical::isOnGround($chunkX * 16, $chunkZ * 16)) {
            $this->generator->getRandom()->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->generator->getLevel()->getSeed());
            foreach ($this->populators as $populator) {
                /** @var $populator Populator */
                $populator->populate($this->generator->getLevel(), $chunkX, $chunkZ, $this->generator->getRandom());
            }

            $chunk = $this->generator->getLevel()->getChunk($chunkX, $chunkZ);
            $biome = self::getBiomeById($chunk->getBiomeId(7, 7));
            $biome->populateChunk($this->generator->getLevel(), $chunkX, $chunkZ, $this->generator->getRandom());
        }
    }

    public function getGenerator(): Generator
    {
        return $this->generator;
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     * @return mixed
     */
    public function generationPopulateChunk(int $chunkX, int $chunkZ, Random $random)
    {
        // TODO: Implement generationPopulateChunk() method.
        return null;
    }

    /**
     * @return string
     */
    public static function getName()
    {
        return 'Earth';
    }
}
