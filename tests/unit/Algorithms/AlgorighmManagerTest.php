<?php
namespace Michaels\Midas\Test\Unit\Algorithms;

use Codeception\Specify;
use Michaels\Midas\Algorithms\Manager as AlgorithmManager;

class AlgorithmManagerTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    public function testFetchAlgorithms()
    {
        $manager = new AlgorithmManager();
        $interface = 'Michaels\Midas\Algorithms\AlgorithmInterface';

        $this->specify("it returns a valid *class-based* algorithm on fetch()", function() use ($manager, $interface) {
            $manager->add('classTest', 'Michaels\Midas\Test\Stubs\ClassBasedAlgorithm');
            $algorithm = $manager->fetch('classTest');

            $this->assertInstanceOf($interface, $algorithm, "Invalid because does not implement AlgorithmInterface");
        });

        $this->specify("it returns a valid *object-based* algorithm on fetch()", function() use ($manager, $interface) {
            $manager->add('objectTest', new \Michaels\Midas\Test\Stubs\ClassBasedAlgorithm);
            $algorithm = $manager->fetch('objectTest');

            $this->assertInstanceOf($interface, $algorithm, "Invalid because does not implement AlgorithmInterface");
        });

        $this->specify("returns a valid *closure-based* algorithm on fetch()", function() use ($manager, $interface) {
            $manager->add('closureTest', function($data, array $params) {
                return true;
            });

            $algorithm = $manager->fetch('closureTest');

            $this->assertInstanceOf($interface, $algorithm, "Invalid because does not implement AlgorithmInterface");
        });
    }

    public function testFetchAlgorithmsExceptions()
    {
        $manager = new AlgorithmManager();

        $this->specify("it throws Exception when class is not found", function () use ($manager) {
            $manager->add('classTest', 'A\Wrong\Class');
            $manager->fetch('classTest');
        }, ['throws' => 'Michaels\Midas\Exceptions\AlgorithmNotFoundException']);

        $this->specify("throws Exception when object doesnt implement AlgorithmInterface", function () use ($manager) {
            $manager->add('invalidClassTest', 'Michaels\Midas\Test\Stubs\InvalidClassBasedAlgorithm');
            $manager->fetch('invalidClassTest');
        }, ['throws' => 'Michaels\Midas\Exceptions\InvalidAlgorithmException']);
    }
}
