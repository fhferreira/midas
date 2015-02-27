<?php
namespace Michaels\Midas\Test\Unit\Commands;

use Codeception\Specify;
use Michaels\Midas\Commands\Manager as CommandManager;

class CommandsManagerTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    public function testFetchCommands()
    {
        $manager = new CommandManager();
        $interface = 'Michaels\Midas\Commands\CommandInterface';

        $this->specify("it returns a valid *class-based* command on fetch()", function() use ($manager, $interface) {
            $manager->add('classTest', 'Michaels\Midas\Test\Stubs\ClassBasedCommand');
            $command = $manager->fetch('classTest');

            $this->assertInstanceOf($interface, $command, "Invalid because does not implement CommandInterface");
        });

        $this->specify("it returns a valid *object-based* command on fetch()", function() use ($manager, $interface) {
            $manager->add('objectTest', new \Michaels\Midas\Test\Stubs\ClassBasedCommand);
            $command = $manager->fetch('objectTest');

            $this->assertInstanceOf($interface, $command, "Invalid because does not implement CommandInterface");
        });

        $this->specify("returns a valid *closure-based* command on fetch()", function() use ($manager, $interface) {
            $manager->add('closureTest', function($data, array $params) {
                return true;
            });

            $command = $manager->fetch('closureTest');

            $this->assertInstanceOf($interface, $command, "Invalid because does not implement CommandInterface");
        });
    }

    public function testFetchCommandsExceptions()
    {
        $manager = new CommandManager();

        $this->specify("it throws an exception when command is not registered", function() use ($manager) {
            $manager->fetch('commandThatDoesNotExist');
        }, ['throws' => 'Michaels\Midas\Exceptions\CommandNotFoundException']);

        $this->specify("it throws Exception when class is not found", function () use ($manager) {
            $manager->add('classTest', 'A\Wrong\Class');
            $manager->fetch('classTest');
        }, ['throws' => 'Michaels\Midas\Exceptions\CommandNotFoundException']);

        $this->specify("throws Exception when object doesnt implement CommandInterface", function () use ($manager) {
            $manager->add('invalidClassTest', 'Michaels\Midas\Test\Stubs\InvalidClassBasedCommand');
            $manager->fetch('invalidClassTest');
        }, ['throws' => 'Michaels\Midas\Exceptions\InvalidCommandException']);
    }
}
