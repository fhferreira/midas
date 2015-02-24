<?php
namespace Michaels\Midas;

use Michaels\Midas\Algorithms\Manager as AlgorithmManager;

class Midas
{

    protected $algorithms;

    /**
     * Create a new Skeleton Instance
     */
    public function __construct()
    {
        $this->algorithms = new AlgorithmManager();
    }

    public function addAlgorithm($alias, $algorithm)
    {
        $this->algorithms->add($alias, $algorithm);
    }

    public function getAllAlgorithms()
    {
        return $this->algorithms->getAll();
    }

    public function isAlgorithm($alias)
    {
        return $this->algorithms->exists($alias);
    }

    public function removeAlgorithm($alias)
    {
        $this->algorithms->remove($alias);
    }

    public function clearAlgorithms()
    {
        $this->algorithms->clear();
    }
}
