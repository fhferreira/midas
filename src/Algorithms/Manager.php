<?php
namespace Michaels\Midas\Algorithms;

use Closure;
use Michaels\Midas\Manager as BaseManager;

/**
 * Manages Algorithms
 *
 * Basic functionality for managing various types of algorithms
 *
 * @package Michaels\Midas\Algorithms
 * @inheritdoc
 */
abstract class Manager extends BaseManager
{
    /**
     * Handles a request for a not found algorithm.
     * Typically, throw some instance of AlgorithmNotFoundException
     *
     * @param string $alias name of the algorithm not found
     * @return void
     */
    abstract protected function handleNotFound($alias);

    /**
     * Handles a request for an invalid algorithm
     * Typically, throw an instance of InvalidAlgortithmException
     *
     * @param $algorithm
     * @return mixed
     */
    abstract protected function handleInvalid($algorithm);

    /**
     * Turns a closure into a generic algorithm.
     * @param $closure
     * @return mixed
     */
    abstract protected function createGeneric($closure);

    /**
     * Fetches a stored algorithm and returns an executable algorithm.
     * @param $alias
     * @return AlgorithmInterface
     */
    public function fetch($alias)
    {
        if (!$this->exists($alias)) {
            $this->handleNotFound($alias);
        }

        $stored = $this->get($alias);
        $algorithm = null;

        if ($stored instanceof Closure) {
            $algorithm = $this->createGeneric($stored);

        } elseif (is_object($stored)) {
            $algorithm =  $stored;

        } elseif (is_string($stored)) {
            if (!class_exists($stored, true)) {
                $this->handleNotFound($stored);
            }

            $class = '\\' . $stored;
            $algorithm =  new $class();
        }

        // Will throw an exception if the algorithm is invalid
        $this->validate($algorithm);

        return $algorithm;
    }

    /**
     * Ensures that a algorithm implements an instance of AlgorithmInterface.
     * @param $algorithm
     */
    public function validate($algorithm)
    {
        if (!$algorithm instanceof AlgorithmInterface) {
            $this->handleInvalid($algorithm);
        }
    }
}
