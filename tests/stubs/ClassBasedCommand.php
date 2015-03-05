<?php
namespace Michaels\Midas\Test\Stubs;

use Michaels\Midas\Commands\CommandInterface;

class ClassBasedCommand implements CommandInterface
{

    public function run($data, array $params = null)
    {
        return true; //
    }
}
