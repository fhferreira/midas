<?php
namespace Michaels\Midas\Test\Unit;

use Michaels\Midas\Manager;
use StdClass;

class AlgorithmManagerTest extends \PHPUnit_Framework_TestCase {

    public function testAddByClassName()
    {
        $manager = new Manager();
        $manager->add('classTest', 'Some\Class');
        $items = $manager->getAll();

        $this->assertArrayHasKey('classTest', $items, 'Array Items does not have key `classTest`');
        $this->assertEquals('Some\Class', $items['classTest']);
    }

    /**
     * Test that true does in fact equal true
     */
    public function testAddItemByClosure()
    {
        $manager = new Manager();
        $manager->add('closureTest', function() {
            return true;
        });

        $items = $manager->getAll();

        $this->assertArrayHasKey('closureTest', $items, 'Array Items does not have key `closureTest`');
        $this->assertEquals(true, $items['closureTest']());
    }

    /**
     * Test that true does in fact equal true
     */
    public function testAddItemByObject()
    {
        $manager = new Manager();
        $manager->add('objectTest', new StdClass());

        $items = $manager->getAll();

        $this->assertArrayHasKey('objectTest', $items, 'Array Items does not have key `objectTest`');
        $this->assertInstanceOf('StdClass', $items['objectTest'], 'Added object is not an object');
    }
}