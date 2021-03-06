<?php
namespace Michaels\Midas\Algorithms;

/**
 * Algorithm contract for all types
 *
 * @package Michaels\Midas\Algorithms
 */
interface AlgorithmInterface
{
    /**
     * Apply the algorithm to a dataset with params
     * @param $data
     * @param array $params
     * @return mixed
     */
    public function run($data, $params = null);
}
