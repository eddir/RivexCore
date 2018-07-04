<?php

namespace rivex\rivexcore\modules\window\primal;


use rivex\rivexcore\modules\window\BaseWindow;

class PluginWindow extends BaseWindow
{

    protected $owner;

    public function __construct($id, $name)
    {
        parent::__construct($id, $name);
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function choice()
    {
        return $this;
    }
}
