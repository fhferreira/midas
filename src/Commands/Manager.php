<?php
namespace Michaels\Midas\Commands;

use Michaels\Midas\Algorithms\Manager as AlgorithmManager;

/**
 * Manages Commands
 *
 * @package Michaels\Midas\Commands
 * @inheritdoc
 */
class Manager extends AlgorithmManager
{
    /**
     * Throw exception if command is not found
     * @inheritdoc
     */
    protected function handleNotFound($alias)
    {
        throw new CommandNotFoundException("`$alias` is not a registered command");
    }

    /**
     * Throw exception if command is not valid
     * @inheritdoc
     */
    protected function handleInvalid($command)
    {
        $classname = class_basename($command);
        throw new InvalidCommandException("`$classname` is not a valid command. It must implement CommandInterface");
    }

    /**
     * Creates a Generic Command instead of Algorithm
     * @inheritdoc
     */
    protected function createGeneric($closure)
    {
        return GenericCommand::create($closure);
    }

    /**
     * Validates as a Command instead of an Algorithm
     * @inheritdoc
     */
    public function validate($command)
    {
        if (!$command instanceof CommandInterface) {
            $this->handleInvalid($command);
        }
    }
}
