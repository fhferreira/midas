<?php
namespace Michaels\Midas\Test\Stubs;

use Michaels\Midas\Algorithms\AlgorithmInterface;

class ClassBasedAlgorithm implements AlgorithmInterface
{

    public function run($data, array $params = null)
    {
        return true;
    }
}
