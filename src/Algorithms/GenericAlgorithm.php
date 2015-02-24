<?php
namespace Michaels\Midas\Algorithms;

use Closure;

class GenericAlgorithm implements AlgorithmInterface
{

    protected $closure;

    public static function create(Closure $closure)
    {
        $algorithm = new static;
        $algorithm->setRun($closure);

        return $algorithm;
    }

    public function run($data, array $params = null)
    {
        $closure = $this->closure;
        return $closure($data, $params);
    }

    private function setRun($closure)
    {
        $this->closure = $closure;
    }
}
