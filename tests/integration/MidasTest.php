<?php

namespace Michaels\Midas\Test\Integration;

use Codeception\Specify;
use Michaels\Midas\Midas;

class MidasTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    protected $midas;

    public function setup()
    {
        $this->midas = new Midas();
    }
    /**
     * Test that true does in fact equal true
     */
    public function testDefaultCreation()
    {
        $this->assertTrue(true);
    }

    public function addAlgoritms()
    {
        $midas->addAlgorithm('mine', 'Some\Class');
        $data = $midas->process($data, 'mine', $params);
//        $data = $midas->mine($data, $params);
//
//        $data = $midas->make($data);
//        $data->mine($params);
    }
}
