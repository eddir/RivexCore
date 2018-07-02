<?php

namespace rivex\rivexcore\utils\exception;

class LogicException extends RivexcoreException
{

    public function __construct($message, $code = 0)
    {
        parent::__construct("Логическая ошибка: $message", $code);
    }

}
