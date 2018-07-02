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

use pocketmine\level\biome\Biome;
use pocketmine\level\biome\ForestBiome;
use rivex\rivexcore\modules\generator\space\location\Earth;
use rivex\rivexcore\modules\generator\space\populator\BushPopulator;
use rivex\rivexcore\modules\generator\space\populator\FallenTreePopulator;
use rivex\rivexcore\modules\generator\space\populator\TreePopulator;

class Forest extends ForestBiome implements Mountainable
{
    const SAKURA_FOREST = 100;
    /** @var string[] * */
    static $types = [
        "Oak Forest",
        "Birch Forest"
    ];
    /** @var int[] * */
    static $ids = [
        Biome::FOREST,
        Biome::BIRCH_FOREST
    ];

    /**
     * Constructs the class
     *
     * @param int $type = 0
     * @param array $infos
     */
    public function __construct($type = 0, array $infos = [0.6, 0.5])
    {
        parent::__construct($type);
        $this->clearPopulators();

        $this->type = $type;

        $bush = new BushPopulator($type);
        $bush->setBaseAmount(10);
        $this->addPopulator($bush);

        $ft = new FallenTreePopulator($type);
        $ft->setBaseAmount(0);
        $ft->setRandomAmount(4);
        $this->addPopulator($ft);
        $trees = new TreePopulator($type);
        $trees->setBaseAmount((null !== @constant(TreePopulator::$types[$type] . "::maxPerChunk")) ? constant(TreePopulator::$types[$type] . "::maxPerChunk") : 5);
        $this->addPopulator($trees);

        $tallGrass = new \pocketmine\level\generator\populator\TallGrass();
        $tallGrass->setBaseAmount(3);

        $this->addPopulator($tallGrass);

        $this->setElevation(63, 69);

        $this->temperature = $infos[0];
        $this->rainfall = $infos[1];
    }

    public function getName(): string
    {
        return str_ireplace(" ", "", self::$types[$this->type]);
    }

    /**
     * Returns the ID relatively.
     *
     * @return int
     */
    public function getId(): int
    {
        return self::$ids[$this->type];
    }

    /**
     * Registers a forest
     *
     * @param string $name
     * @param string $treeClass
     * @param array $infos
     * @return bool
     */
    public static function registerForest(string $name, string $treeClass, array $infos): bool
    {
        self::$types[] = str_ireplace("tree", "", explode("\\", $treeClass)[count(explode("\\", $treeClass))]) . " Forest";
        TreePopulator::$types[] = $treeClass;
        self::$ids[] = self::SAKURA_FOREST + (count(self::$types) - 2);
        Earth::registerBiome(new Forest(count(self::$types) - 1, $infos));
        return true;
    }
}
