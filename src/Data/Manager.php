<?php
namespace Michaels\Midas\Data;

use \Michaels\Midas\Manager as BaseManager;

class Manager extends BaseManager
{
    public function fetch($alias)
    {
        return new RawData($this->get($alias));
    }
}
