<?php

namespace Michaels\Midas\Test\Integration;

use Codeception\Specify;
use Michaels\Midas\Midas;
use Michaels\Midas\Test\Stubs\ClassBasedCommand;

class MidasTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    public function testConfigureMidas()
    {
        $this->specify("it instantiates with default configs", function () {
            $midas = new Midas();

            $this->assertTrue($midas->config('test_dummy'), "failed to get default set item");
            $this->assertEquals('response', $midas->config('nonexistent', 'response'), 'failed to return a fallback value');
        });

        $this->specify("it instantiates with user-provided configs", function () {
            $midas = new Midas(['test_dummy' => false]);

            $this->assertFalse($midas->config('test_dummy'), 'failed to set a user defined item on instantiation');
            $this->assertArrayHasKey('reserved_words', $midas->getAllConfig(), 'failed to cascade config items');
            $this->assertTrue($midas->getDefaultConfig('test_dummy'), 'failed to get default config item');
        });

        $this->specify("it sets config items", function () {
            $midas = new Midas();

            $midas->setConfig(['test_dummy' => false]);
            $this->assertFalse($midas->config('test_dummy'), 'failed to reset all configs');

            $midas->setConfig('test_dummy', true);
            $this->assertTrue($midas->config('test_dummy'), 'failed to set a single config item');

            $midas->setConfig('new_key', 'value');
            $this->assertEquals('value', $midas->config('new_key'), 'failed to set a non-existent single config item');
        });
    }

    public function testManageCommands()
    {
        $midas = new Midas();

        $this->specify("it adds commands", function () use ($midas) {
            $midas->addCommand('classTest1', 'Michaels\Midas\Test\Stubs\ClassBasedCommand');
            $midas->addCommand('objectTest1', new ClassBasedCommand());
            $midas->addCommand('closureTest1', function ($data, array $params = null) {
                return TRUE;
            });

            $commands = $midas->getAllCommands();
            $this->assertArrayHasKey('classTest1', $commands, 'class-based command not set');
            $this->assertEquals('Michaels\Midas\Test\Stubs\ClassBasedCommand', $commands['classTest1']);

            $this->assertArrayHasKey('objectTest1', $commands, 'object-based command not set');
            $this->assertInstanceOf('Michaels\Midas\Test\Stubs\ClassBasedCommand', $commands['objectTest1']);

            $this->assertArrayHasKey('closureTest1', $commands, 'closure-based command not set');
            $this->assertEquals(TRUE, $commands['closureTest1']('nothing', []));
        });

        $this->specify("it verifies that commands exists", function () use ($midas) {
            $exists = $midas->isCommand('classTest1');
            $doesNotExist = $midas->isCommand('doesNotExist');

            $this->assertTrue($exists, 'failed to verify existing command');
            $this->assertFalse($doesNotExist, 'failed to verify non-existence of command');
        });

        $this->specify("it updates (sets) commands", function () use ($midas) {
            $midas->setCommand('newCommand', 'Value');

            $commands = $midas->getAllCommands();
            $this->assertArrayHasKey('newCommand', $commands, 'class-based command not set');
            $this->assertEquals('Value', $commands['newCommand']);
        });

        $this->specify("it deletes and clears commands", function () use ($midas) {
            $midas->removeCommand('classTest1');
            $commands = $midas->getAllCommands();

            $this->assertArrayNotHasKey('classTest1', $commands, 'failed to remove a single command');

            $midas->clearCommands();
            $emptyCommands = $midas->getAllCommands();
            $this->assertEmpty($emptyCommands, 'failed to clear commands');
        });

        $this->specify("it fetches command", function () use ($midas) {
            $midas->addCommand('fetchedCommand', function($data, $params){
                return true;
            });

            $command = $midas->fetchCommand('fetchedCommand');

            $this->assertInstanceOf('Michaels\Midas\Commands\GenericCommand', $command, 'failed to fetch a generic command');
        });

        $this->specify("it allows closures to use command instance", function () use ($midas) {
            $midas->addCommand('helpersCommand', function($data, $params, $command){
                return $command;
            });

            $this->assertInstanceOf('Michaels\Midas\Commands\GenericCommand', $midas->helpersCommand(), 'failed to return an instance of its own command');
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

            $this->assertInstanceOf('Michaels\Midas\Data\RefinedData', $actual, "failed to return a RefinedDataObject");
            $this->assertEquals('a string', $actual['string'], "failed to read `string` from complex data");
            $this->assertEquals('A', $actual['multiArray']['test'], "failed to read `string` from complex data");
        });

        $this->specify("it returns data as standard if requested", function() use ($midas) {
            $actual = $midas->complexArray(null, null, false);

            $this->assertNotInstanceOf('Michaels\Midas\Data\RefinedData', $actual, "failed to return a non RefinedDataObject");
            $this->assertEquals('a string', $actual['string'], "failed to read `string` from complex data");
            $this->assertEquals('A', $actual['multiArray']['test'], "failed to read `string` from complex data");
        });
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage `data` is a reserved word
     */
    public function testCommandExceptions()
    {
        $midas = new Midas();

        $midas->addCommand('data', 'Doesnt\Matter');
    }

    public function testManageDataSets()
    {
        $midas = new Midas();

        $testData = [
            'some' => true,
            'complex' => [
                'data' => 123
            ]
        ];

        $this->specify("it adds data", function() use ($midas, $testData) {
            $midas->addData('data1', $testData);

            $data = $midas->getAllData();
            $this->assertEquals($testData, $data['data1']);
        });

        $this->specify("it verifies that data exists", function() use ($midas) {
            $midas->addData('exists', 'value');
            $exists = $midas->isData('exists');
            $doesNotExist = $midas->isCommand('doesNotExist');

            $this->assertTrue($exists, 'failed to verify existing data');
            $this->assertFalse($doesNotExist, 'failed to verify non-existence of data');
        });

        $this->specify("it updates (sets) data", function() use ($midas) {
            $midas->setData('setdata', 'value');

            $data = $midas->getData('setdata');
            $this->assertEquals('value', $data, 'failed to set data');
        });

        $this->specify("it deletes and clears data", function() use ($midas) {
            $midas->addData('deletedata', 'value');
            $midas->addData('deletedata2', 'value2');

            $midas->removeData('deletedata');

            $this->assertArrayNotHasKey('deletedata', $midas->getAllData(), 'failed to remove a single data set');

            $midas->clearData();
            $emptyData = $midas->getAllData();
            $this->assertEmpty($emptyData, 'failed to clear data');
        });

        $this->specify("it fetches complex data as a RawDataObject", function() use ($midas) {
            $testData = [
                'string' => 'abc',
                'int' => 123,
                'bool' => true,
                'array' => [
                    'a' => 'A',
                    'b' => 'B"'
                ]
            ];

            $midas->addData('testComplexData', $testData);

            $rawData = $midas->data('testComplexData', true);
            $this->assertInstanceOf('Michaels\Midas\Data\RawData', $rawData, 'failed to fetch raw data as RawData');
            $this->assertEquals($testData, $rawData->toArray());
        });

        $this->specify("it fetches simple data as a RawDataObject", function() use ($midas) {
            $testStringData = 'test data';
            $testBoolData = true;
            $testIntData = 123;

            $midas->addData('stringData', $testStringData);
            $midas->addData('boolData', $testBoolData);
            $midas->addData('intData', $testIntData);

            $actualStringData = $midas->data('stringData', true);
            $actualBoolData = $midas->data('boolData', true);
            $actualIntData = $midas->data('intData', true);

            $this->assertInstanceOf('Michaels\Midas\Data\RawData', $actualStringData, 'failed to fetch string raw data as RawData');
            $this->assertInstanceOf('Michaels\Midas\Data\RawData', $actualBoolData, 'failed to fetch boolean raw data as RawData');
            $this->assertInstanceOf('Michaels\Midas\Data\RawData', $actualIntData, 'failed to fetch integer raw data as RawData');

            $this->assertEquals($testStringData, $actualStringData->value(), 'string');
            $this->assertEquals($testBoolData, $actualBoolData->value(), 'bool');
            $this->assertEquals($testIntData, $actualIntData->value(), 'int');
        });
    }
}