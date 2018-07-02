<?php

namespace rivex\rivexcore\utils\exception;

use pocketmine\utils\ServerException;

class RivexcoreException extends ServerException
{

    public function __construct($message = null, $code = 0)
    {
        // TODO
        parent::__construct($message, $code);
    }

}
