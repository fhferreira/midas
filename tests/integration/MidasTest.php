<?php

namespace Michaels\Midas\Test\Integration;

use Codeception\Specify;
use Michaels\Midas\Midas;
use Michaels\Midas\Test\Stubs\ClassBasedCommand;

class MidasTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    protected $midas;

    public function testManageCommands()
    {
        $midas = new Midas();

        $this->specify("it adds aglorithms", function() use ($midas) {
            $midas->addCommand('classTest1', 'Michaels\Midas\Test\Stubs\ClassBasedCommand');
            $midas->addCommand('objectTest1', new ClassBasedCommand());
            $midas->addCommand('closureTest1', function ($data, array $params = null) {
                return true;
            });

            $commands = $midas->getAllCommands();
            $this->assertArrayHasKey('classTest1', $commands, 'class-based command not set');
            $this->assertEquals('Michaels\Midas\Test\Stubs\ClassBasedCommand', $commands['classTest1']);

            $this->assertArrayHasKey('objectTest1', $commands, 'object-based command not set');
            $this->assertInstanceOf('Michaels\Midas\Test\Stubs\ClassBasedCommand', $commands['objectTest1']);

            $this->assertArrayHasKey('closureTest1', $commands, 'closure-based command not set');
            $this->assertEquals(true, $commands['closureTest1']('nothing', []));
        });

        $this->specify("it verifies that commands exists", function() use ($midas) {
            $exists = $midas->isCommand('classTest1');
            $doesNotExist = $midas->isCommand('doesNotExist');

            $this->assertTrue($exists, 'failed to verify existing command');
            $this->assertFalse($doesNotExist, 'failed to verify non-existence of command');
        });

        // Delete Commands
        $this->specify("it deletes and clears commands", function() use ($midas) {
            $midas->removeCommand('classTest1');
            $commands = $midas->getAllCommands();

            $this->assertArrayNotHasKey('classTest1', $commands, 'failed to remove a single command');

            $midas->clearCommands();
            $emptyCommands = $midas->getAllCommands();
            $this->assertEmpty($emptyCommands, 'failed to clear commands');
        });
    }
}
