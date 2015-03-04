<?php
namespace Michaels\Midas\Data;

use Illuminate\Support\Collection;

/**
 * Holds raw data with helpers
 * @package Michaels\Midas\Data
 */
class RawData extends Collection
{
    /**
     * Create a new RawData Instance
     * @param array|mixed $data
     */
    public function __construct($data)
    {
        if (!is_array($data)) {
            $this->items['value'] = $data;
        } else {
            parent::__construct($data);
        }
    }

    /**
     * Return the value of RawData if it is a single primitive (not a collection)
     * @return mixed
     */
    public function value()
    {
        return $this->items['value'];
    }
}
