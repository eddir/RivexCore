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

namespace rivex\rivexcore\modules\generator\space\populator;


use pocketmine\block\Block;
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\level\Level;
use pocketmine\utils\Random;

class DeadbushPopulator extends AmountPopulator
{
    /** @var ChunkManager */
    protected $level;

    /**
     * Populates the chunk
     *
     * @param ChunkManager $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     * @return void
     */
    public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random)
    {
        $this->level = $level;
        $amount = $this->getAmount($random);
        for ($i = 0; $i < $amount; $i++) {
            $x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
            $z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
            if (!in_array($level->getChunk($chunkX, $chunkZ)->getBiomeId(abs($x % 16), ($z % 16)), [40, 39, Biome::DESERT])) continue;
            $y = $this->getHighestWorkableBlock($x, $z);
            if ($y !== -1 && $level->getBlockIdAt($x, $y - 1, $z) == Block::SAND) {
                $level->setBlockIdAt($x, $y, $z, Block::DEAD_BUSH);
                $level->setBlockDataAt($x, $y, $z, 1);
            }
        }
    }

    /**
     * Gets the top block (y) on an x and z axes
     * @param $x
     * @param $z
     * @return int
     */
    protected function getHighestWorkableBlock($x, $z)
    {
        for ($y = Level::Y_MAX - 1; $y > 0; --$y) {
            $b = $this->level->getBlockIdAt($x, $y, $z);
            if ($b === Block::DIRT or $b === Block::GRASS or $b === Block::SAND or $b === Block::SANDSTONE or $b === Block::HARDENED_CLAY or $b === Block::STAINED_HARDENED_CLAY) {
                break;
            } elseif ($b !== Block::AIR) {
                return -1;
            }
        }
        return ++$y;
    }
}
