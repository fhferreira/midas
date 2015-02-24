<?php
namespace Michaels\Midas\Algorithms;

use Closure;
use Michaels\Midas\Algorithms\AlgorithmInterface;
use Michaels\Midas\Exceptions\AlgorithmNotFoundException;
use Michaels\Midas\Exceptions\InvalidAlgorithmException;
use Michaels\Midas\Manager as BaseManager;

class Manager extends BaseManager
{
    public function fetch($alias)
    {
        $stored = $this->get($alias);
        $algorithm = null;

        if ($stored instanceof Closure) {
            $algorithm = GenericAlgorithm::create($stored);

        } elseif (is_object($stored)) {
            $algorithm =  $stored;

        } elseif (is_string($stored)) {
            if (!class_exists($stored, true)) {
                throw new AlgorithmNotFoundException();
            }

            $class = '\\' . $stored;
            $algorithm =  new $class();
        }

        $this->validate($algorithm);

        return $algorithm;
    }

    public function validate($algorithm)
    {
        if (!$algorithm instanceof AlgorithmInterface) {
            throw new InvalidAlgorithmException();
        }
    }
}
