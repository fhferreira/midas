<?php
namespace Michaels\Midas\Test\Stubs;

use Michaels\Midas\Commands\CommandInterface;

class ClassBasedCommand implements CommandInterface
{

    public function run($data, $params = null)
    {
        return true;
    }
}
