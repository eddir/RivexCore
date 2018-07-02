<?php
/**
 * Created by PhpStorm.
 * User: eddir
 * Date: 12.06.18
 * Time: 21:25
 */

namespace rivex\rivexcore\modules\generator\space\location;


use pocketmine\utils\Random;

interface Location
{

    /**
     * @param int $x
     * @param int $z
     * @param Random $random
     */
    public function generateChunk(int $x, int $z, Random $random): void;

    /**
     * @param int $x
     * @param int $z
     * @param Random $random
     */
    public function populateChunk(int $x, int $z, Random $random): void;

    /**
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     * @return mixed
     */
    public function generationPopulateChunk(int $chunkX, int $chunkZ, Random $random);

    /**
     * @return string
     */
    public static function getName();

}