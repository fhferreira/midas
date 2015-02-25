<?php
namespace Michaels\Midas\Commands;

use Closure;
use Michaels\Midas\Commands\CommandInterface;
use Michaels\Midas\Exceptions\AlgorithmNotFoundException;
use Michaels\Midas\Exceptions\CommandNotFoundException;
use Michaels\Midas\Exceptions\InvalidAlgorithmException;
use Michaels\Midas\Exceptions\InvalidCommandException;
use Michaels\Midas\Manager as BaseManager;

class Manager extends BaseManager
{
    public function fetch($alias)
    {
        $stored = $this->get($alias);
        $command = null;

        if ($stored instanceof Closure) {
            $command = GenericCommand::create($stored);

        } elseif (is_object($stored)) {
            $command =  $stored;

        } elseif (is_string($stored)) {
            if (!class_exists($stored, true)) {
                throw new CommandNotFoundException();
            }

            $class = '\\' . $stored;
            $command =  new $class();
        }

        $this->validate($command);

        return $command;
    }

    public function validate($command)
    {
        if (!$command instanceof CommandInterface) {
            throw new InvalidCommandException();
        }
    }
}
