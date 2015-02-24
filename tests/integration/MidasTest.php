<?php

namespace Michaels\Midas\Test\Integration;

use Codeception\Specify;
use Michaels\Midas\Midas;
use Michaels\Midas\Test\Stubs\ClassBasedAlgorithm;

class MidasTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    protected $midas;

    public function testManageAlgoritms()
    {
        $midas = new Midas();

        $this->specify("it adds aglorithms", function() use ($midas) {
            $midas->addAlgorithm('classTest1', 'Michaels\Midas\Test\Stubs\ClassBasedAlgorithm');
            $midas->addAlgorithm('objectTest1', new ClassBasedAlgorithm());
            $midas->addAlgorithm('closureTest1', function ($data, array $params = null) {
                return true;
            });

            $algorithms = $midas->getAllAlgorithms();
            $this->assertArrayHasKey('classTest1', $algorithms, 'class-based algorithm not set');
            $this->assertEquals('Michaels\Midas\Test\Stubs\ClassBasedAlgorithm', $algorithms['classTest1']);

            $this->assertArrayHasKey('objectTest1', $algorithms, 'object-based algorithm not set');
            $this->assertInstanceOf('Michaels\Midas\Test\Stubs\ClassBasedAlgorithm', $algorithms['objectTest1']);

            $this->assertArrayHasKey('closureTest1', $algorithms, 'closure-based algorithm not set');
            $this->assertEquals(true, $algorithms['closureTest1']('nothing', []));
        });

        $this->specify("it verifies that algorithms exists", function() use ($midas) {
            $exists = $midas->isAlgorithm('classTest1');
            $doesNotExist = $midas->isAlgorithm('doesNotExist');

            $this->assertTrue($exists, 'failed to verify existing algorithm');
            $this->assertFalse($doesNotExist, 'failed to verify non-existence of algorithm');
        });

        // Delete Algorithms
        $this->specify("it deletes and clears algorithms", function() use ($midas) {
            $midas->removeAlgorithm('classTest1');
            $algorithms = $midas->getAllAlgorithms();

            $this->assertArrayNotHasKey('classTest1', $algorithms, 'failed to remove a single algorithm');

            $midas->clearAlgorithms();
            $emptyAlgorithms = $midas->getAllAlgorithms();
            $this->assertEmpty($emptyAlgorithms, 'failed to clear algorithms');
        });
    }
}
