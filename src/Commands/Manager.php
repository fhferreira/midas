<?php
namespace Michaels\Midas\Commands;

use Michaels\Midas\Algorithms\Manager as AlgorithmManager;

class Manager extends AlgorithmManager
{

    protected function handleNotFound($alias)
    {
        throw new CommandNotFoundException("`$alias` is not a registered command");
    }

    protected function handleInvalid($algorithm)
    {
        $classname = class_basename($algorithm);
        throw new InvalidCommandException("`$classname` is not a valid command. It must implement CommandInterface");
    }

    protected function createGeneric($closure)
    {
        return GenericCommand::create($closure);
    }

    public function validate($algorithm)
    {
        if (!$algorithm instanceof CommandInterface) {
            $this->handleInvalid($algorithm);
        }
    }
}
