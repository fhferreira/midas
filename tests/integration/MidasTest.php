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

        $this->specify("it adds commands", function() use ($midas) {
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

    public function testProcessDataThroughCommands()
    {
        $midas = new Midas();
        $midas->addCommands([
            'add' => function($data, $params = null) {
                return $data + 10;
            },

            'subtract' => function($data, $params = null) {
                return $data - 10;
            },

            'params' => function ($data, $params = null) {
                return $data + $params[0];
            },

            'text' => function ($data, $params = null) {
                return $data . " " . $params['text'] . ".";
            },

            'complexArray' => function ($data, $params = null) {
                return [
                    'int' => 1,
                    'bool' => true,
                    'string' => 'a string',
                    'array' => [3, 1, 7, 3],
                    'multiArray' => ['test' => 'A'],
                ];
            }
        ]);

        $this->specify("it processes data through a magic method with no params", function() use ($midas) {
            $actual = $midas->add(1);
            $this->assertEquals(11, $actual, "failed to process `add` command");

            $actual = $midas->subtract(20);
            $this->assertEquals(10, $actual, "failed to process `subract` command");
        });

        $this->specify("it processes data through a magic method with params", function() use ($midas) {
            $actual = $midas->params(2, [2]);
            $this->assertEquals(4, $actual, "failed to process `params` command with parameter");

            $actual = $midas->text("test", ['text' => 'sentence']);
            $this->assertEquals('test sentence.', $actual, "failed to process `text` command with named params");
        });

        $this->specify("it returns complex data as a DataCollection", function() use ($midas) {
            $actual = $midas->complexArray(null);

            $this->assertInstanceOf('Michaels\Midas\RefinedData', $actual, "failed to return a RefinedDataObject");
            $this->assertEquals('a string', $actual['string'], "failed to read `string` from complex data");
            $this->assertEquals('A', $actual['multiArray']['test'], "failed to read `string` from complex data");
        });

        $this->specify("it returns data as standard if requested", function() use ($midas) {
            $actual = $midas->complexArray(null, null, false);

            $this->assertNotInstanceOf('Michaels\Midas\RefinedData', $actual, "failed to return a non RefinedDataObject");
            $this->assertEquals('a string', $actual['string'], "failed to read `string` from complex data");
            $this->assertEquals('A', $actual['multiArray']['test'], "failed to read `string` from complex data");
        });
    }
}
