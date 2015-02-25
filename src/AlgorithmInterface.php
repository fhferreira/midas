<?php
namespace Michaels\Midas;

interface AlgorithmInterface
{
    public function run($data, array $params = null);
}
