<?php

namespace rivex\rivexcore\modules\generator\space;

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


use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\noise\Noise;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\level\generator\populator\Populator;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Random;
use rivex\rivexcore\modules\generator\space\location\Earth;
use rivex\rivexcore\modules\generator\space\location\Jupiter;
use rivex\rivexcore\modules\generator\space\location\Location;
use rivex\rivexcore\modules\generator\space\location\Mars;
use rivex\rivexcore\modules\generator\space\location\Mercury;
use rivex\rivexcore\modules\generator\space\location\Neptune;
use rivex\rivexcore\modules\generator\space\location\Saturn;
use rivex\rivexcore\modules\generator\space\location\Sun;
use rivex\rivexcore\modules\generator\space\location\Uranus;
use rivex\rivexcore\modules\generator\space\location\Venus;
use rivex\rivexcore\modules\generator\space\populator\CavePopulator;
use rivex\rivexcore\modules\window\primal\ServersWindow;


class SpaceGenerator extends Generator
{
    const NAME = 'space';
    public static $GAUSSIAN_KERNEL = null;

    //TODO: использовать getName()
    private static $locationNames = array(
        Sun::class,
        Earth::class,
        Jupiter::class,
        Mars::class,
        Mercury::class,
        Neptune::class,
        Saturn::class,
        Uranus::class,
        Venus::class
    );
    private static $SMOOTH_SIZE = 2;
    /** @var Level */
    protected $level;
    /** @var Random */
    protected $random;
    private $locations = [];

    // Ядро для генерации неровностей поверхности
    /** @var Populator[] */
    private $generationPopulators = [];

    // Хрен его знает что это, но без него не работает. Так ещё и псевдоконстанта.
    private $noiseBase;
    private $options;

    /**
     * SpaceGenerator constructor.
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->options = $settings;
        if (self::$GAUSSIAN_KERNEL === null) {
            self::generateKernel();
        }
    }

    private static function generateKernel()
    {
        self::$GAUSSIAN_KERNEL = [];
        $bellSize = 1 / self::$SMOOTH_SIZE;
        $bellHeight = 2 * self::$SMOOTH_SIZE;
        for ($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; ++$sx) {
            self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] = [];
            for ($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; ++$sz) {
                $bx = $bellSize * $sx;
                $bz = $bellSize * $sz;
                self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE][$sz + self::$SMOOTH_SIZE] = $bellHeight * exp(-($bx * $bx + $bz * $bz) / 2);
            }
        }
    }

    /**
     * @return int
     */
    public static function getSmoothSize(): int
    {
        return self::$SMOOTH_SIZE;
    }

    /**
     * @return null
     */
    public static function getKernel()
    {
        return self::$GAUSSIAN_KERNEL;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return [];
    }

    public function getRandom(): Random
    {
        return $this->random;
    }

    /**
     * @return Noise
     */
    public function getNoiseBase(): Noise
    {
        return $this->noiseBase;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    // Favorite: yellow #CONCRETEPOWDER
    //     setBlock($x, $y, $z, Block::CONCRETEPOWDER, 4);

    /**
     * @param ChunkManager $level
     * @param Random $random
     */
    public function init(ChunkManager $level, Random $random): void
    {
        $this->level = $level;
        $this->random = $random;
        $this->random->setSeed($this->level->getSeed());
        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 32);
        $this->random->setSeed($this->level->getSeed());

        foreach (self::$locationNames as $name => $location) {
            $this->locations[] = new $location($this);
        }

        $cave = new CavePopulator ();
        $cave->setBaseAmount(0);
        $cave->setRandomAmount(2);
        $this->generationPopulators[] = $cave;
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function generateChunk(int $chunkX, int $chunkZ): void
    {
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
        $planet = $this->getPlanetAt($chunkX * 16, $chunkZ * 16);
        $planet->generateChunk($chunkX, $chunkZ, $this->random);

        foreach ($this->generationPopulators as $populator) {
            $populator->populate($this->level, $chunkX, $chunkZ, $this->random);
        }

        $planet->generationPopulateChunk($chunkX, $chunkZ, $this->random);
        /**$noise = Generator::getFastNoise2D($this->noiseBase, 16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);
         *
         * for ($x = 0; $x < 16; $x++) {
         * for ($z = 0; $z < 16; $z++) {
         * $maxY = ($noise[$x][$z] * 2) + 100;
         * for ($y = 0; $y < $maxY; $y++) {
         * $chunk->setBlock($x, $y, $z, Block::CONCRETEPOWDER, 4);
         * }
         * }
         * }*/
        /*for ($x = 0; $x < 16; $x++) {
            for ($z = 0; $z < 16; $z++) {
                $xm = sin(($chunkX * 16 + $x)/30);
                $zm = sin(($chunkZ * 16 + $z)/30);
                $ym = (($xm > $zm ? $xm : $zm) + 2) * 10;
                for ($y = 0; $y < $ym; $y++) {
                    $chunk->setBlockId($x, $y, $z, Block::GRASS);
                }
            }
        }*/
    }

    /**
     * @param int $x
     * @param int $z
     * @param ChunkManager $level
     * @return string
     */
    public static function getLocationAt(int $x, int $z, ChunkManager $level): string
    {
        return self::$locationNames[(
        abs($level->getSeed() % 9 + floor($x / 1024) + floor($z / 1024))
        ) % 9];
    }

    /**
     * @param int $x
     * @param int $z
     * @return Location
     */
    private function getPlanetAt(int $x, int $z): Location
    {
        return $this->locations[(
            abs($this->level->getSeed() % 9 + floor($x / 1024) + floor($z / 1024))
        ) % 9];
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function populateChunk(int $chunkX, int $chunkZ): void
    {
        $planet = $this->getPlanetAt($chunkX * 16, $chunkZ * 16);
        $planet->populateChunk($chunkX, $chunkZ, $this->random);
    }

    /**
     * @return Vector3
     */
    public function getSpawn(): Vector3
    {
        return new Vector3(0, 128, 0);
    }

    /**
     * @return ChunkManager
     */
    public function getLevel(): ChunkManager
    {
        return $this->level;
    }

}
