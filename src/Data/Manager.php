<?php
namespace Michaels\Midas\Data;

use \Michaels\Midas\Manager as BaseManager;

class Manager extends BaseManager
{
    /**
     * Return data as a RawDataObject
     *
     * @param string $alias
     * @return RawData
     */
    public function fetch($alias)
    {
        return new RawData($this->get($alias));
    }
}
