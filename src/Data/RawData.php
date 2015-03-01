<?php
namespace Michaels\Midas\Data;

use Illuminate\Support\Collection;

class RawData extends Collection
{
    public function __construct($data)
    {
        if (!is_array($data)) {
            $this->items['value'] = $data;
        } else {
            parent::__construct($data);
        }
    }
    public function value()
    {
        return $this->items['value'];
    }
}
