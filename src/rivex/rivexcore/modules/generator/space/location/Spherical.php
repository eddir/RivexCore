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
use pocketmine\level\format\Chunk;
use pocketmine\utils\Random;
use rivex\rivexcore\modules\generator\space\SpaceGenerator as Generator;


class Spherical
{

    // Жидкость, которая заполняет мировой океан
    private $luquid = [Block::WATER, 0];

    // Эти значения сильно зависят друг от друга. Поэтому замена одной переменной без корректировки
    // других может привести к непредсказуемым последствиям.
    const RADIUS = 512;
    const DOWN = 480;
    const FLAT = 300;

    // Максимальная высота почвы на шаре от 0:0 до FLAT.
    const FLAT_HEIGHT = 100;

    // Минимальная высота почвы
    const FLAT_LOW = 80;

    // Максимальная высота уровня моря
    const WATER_HEIGHT = 87;

    // Коэффициент для графика sqrt(x). Расчитывается по формуле:
    // (self::HEIGHT / sqrt(self::DOWN - self::FLAT) ).
    // То есть, соотношение высоты к квадратному корню ширины хорды.
    const SMOOTHING = 7.4;

    /**
     * Создаёт фрагмент шара на заданном чанке. Квадрат шара это (self::DIAMETER / 16) квадратных чанков.
     * Объект состоит из космической пустоты - self::DOWN блоков от центра в радиусе,
     * плавного спуска от ровности до пустоты - от self::FLAT блоков до self::DOWN,
     * равнины, самой земли на шаре (это иллюзия шара - только бока закруглёны, а центр плоский оказывается)
     * - равнина идёт от центра с относительной координатой 0:0 до self::FLAT в радиусе.
     * @param Chunk $chunk
     * @param $noise
     * @param array $block
     * @param array $luquid
     * @param Random $random
     */
    public function generateSphere(Chunk $chunk, $noise, array $block, array $luquid, Random $random)
    {
        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $remote = $this->getRemoteness($x + $chunk->getX() * 16, $z + $chunk->getZ() * 16);
                if ($remote < self::DOWN) {
                    if ($remote < self::FLAT) {
                        $maxY = self::FLAT_HEIGHT;
                    } else {
                        $maxY = self::SMOOTHING * sqrt(self::DOWN - $remote) + $random->nextBoundedInt(2);
                    }
                } else {
                    $maxY = 0;
                }

                $minSum = 0;
                $maxSum = 0;
                $weightSum = 0;
                $smoothSize = Generator::getSmoothSize();
                for ($sx = -$smoothSize; $sx <= $smoothSize; ++$sx) {
                    for ($sz = -$smoothSize; $sz <= $smoothSize; ++$sz) {
                        $weight = Generator::getKernel()[$sx + $smoothSize][$sz + $smoothSize];
                        $minSum += (self::FLAT_LOW - 1) * $weight;
                        $maxSum += self::FLAT_HEIGHT * $weight;
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
                        $chunk->setBlock($x, $y, $z, $block[0], $block[1]);
                    } elseif ($y <= self::WATER_HEIGHT) {
                        $chunk->setBlock($x, $y, $z, $luquid[0], $luquid[1]);
                    }
                }
            }
        }
    }

    /**
     * @param Chunk $chunk
     * @param $block
     * @param $liquid
     * @param $random
     */
    public function populateSphere(Chunk $chunk, $block, $liquid, $random)
    {
        for ($x = 0; $x < 16; $x++) {
            for ($z = 0; $z < 16; $z++) {
                $remote = $this->getRemoteness($x + $chunk->getX() * 16, $z + $chunk->getZ() * 16);
                if ($remote >= self::FLAT) {
                    continue;
                }
                for ($y = self::WATER_HEIGHT; $y > 0; $y--) {
                    if ($chunk->getBlockId($x, $y, $z) == 0) {
                        $chunk->setBlock($x, $y, $z, $liquid[0], $liquid[1]);
                    } else {
                        break;
                    }
                }
            }
        }
    }
    // // // // // // // // //
    //  [512 - 400) : 0     //
    //  (400 - 300) : sqrt  //
    //  (300 - 0  ] : 100   //
    // // // // // // // // //
    /**
     * Возвращает расстояние до центра шара
     * @param $x
     * @param $z
     * @return float
     */
    public static function getRemoteness($x, $z)
    {
        $xc = abs(self::RADIUS - abs($x % (2 * self::RADIUS)));
        $zc = abs(self::RADIUS - abs($z % (2 * self::RADIUS)));
        return sqrt($xc * $xc + $zc * $zc);
    }

    /**
     * Возвращает True, если точка находится в зоне пустоты.
     * Возвращает False, если точка находится на шаре.
     * @param $x
     * @param $z
     * @return bool
     */
    public static function isBorder($x, $z): bool
    {
        return self::getRemoteness($x, $z) > self::DOWN ? true : false;
    }

    /**
     * Возвращает True, если точка находится в зоне плоского пространства.
     * Возвращает False, если точка находится не в плоской зоне.
     * @param $x
     * @param $z
     * @return bool
     */
    public static function isOnGround($x, $z): bool
    {
        return self::getRemoteness($x, $z) < self::FLAT ? true : false;
    }

}
