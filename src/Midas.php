<?php
namespace Michaels\Midas;

use Michaels\Midas\Commands\Manager as CommandManager;

class Midas
{

    protected $commands;
//    protected $data;
//    protected $questions;

    /**
     * Create a new Midas Instance
     */
    public function __construct()
    {
        $this->commands = new CommandManager();
        // Saves logic as algorithm

//        $this->questions = new QuestionManager();
//        $this->data = new DataManager();
    }

    public function addCommand($alias, $command)
    {
        $this->commands->add($alias, $command);
    }

    public function addCommands(array $commands)
    {
        $this->commands->add($commands);
    }

    public function getAllCommands()
    {
        return $this->commands->getAll();
    }

    public function isCommand($alias)
    {
        return $this->commands->exists($alias);
    }

    public function removeCommand($alias)
    {
        $this->commands->remove($alias);
    }

    public function clearCommands()
    {
        $this->commands->clear();
    }
}
