<?php
namespace Michaels\Midas;

use Michaels\Midas\Commands\Manager as CommandManager;
use Michaels\Midas\Data\Manager as DataManager;
use Michaels\Midas\Data\RefinedData;

class Midas
{

    protected $commands;
    protected $data;
//    protected $questions;

    /**
     * Create a new Midas Instance
     */
    public function __construct()
    {
        $this->commands = new CommandManager();
        // Saves logic as algorithm

//        $this->questions = new QuestionManager();
        $this->data = new DataManager();
    }

    public function addCommand($alias, $command)
    {
        $this->commands->add($alias, $command);
    }

    public function addCommands(array $commands)
    {
        $this->commands->add($commands);
    }

    public function getCommand($alias)
    {
        $this->commands->get($alias);
    }

    public function getAllCommands()
    {
        return $this->commands->getAll();
    }

    public function setCommand($alias, $command)
    {
        $this->commands->set($alias, $command);
    }

    public function removeCommand($alias)
    {
        $this->commands->remove($alias);
    }

    public function clearCommands()
    {
        $this->commands->clear();
    }

    public function isCommand($alias)
    {
        return $this->commands->exists($alias);
    }

    public function fetchCommand($alias)
    {
        return $this->commands->fetch($alias);
    }

    public function addData($alias, $data = null)
    {
        $this->data->add($alias, $data);
    }

    public function getData($alias)
    {
        return $this->data->get($alias);
    }

    public function getAllData()
    {
        return $this->data->getAll();
    }

    public function setData($alias, $data)
    {
        $this->data->set($alias, $data);
    }

    public function removeData($alias)
    {
        $this->data->remove($alias);
    }

    public function clearData()
    {
        $this->data->clear();
    }

    public function isData($alias)
    {
        return $this->data->exists($alias);
    }

    public function data($alias)
    {
        return $this->data->fetch($alias);
    }

    public function __call($name, $arguments)
    {
        $command = $this->commands->fetch($name);
        $data = $arguments[0];
        $params = (isset($arguments[1])) ? $arguments[1] : null;
        $returnRefined = (isset($arguments[2])) ? $arguments[2] : true;

        $result = $command->run($data, $params);

        return $this->ensureDataCollection($result, $returnRefined);
    }

    private function ensureDataCollection($data, $returnRefined)
    {
        if (is_array($data) and $returnRefined) {
            return new RefinedData($data);
        } else {
            return $data;
        }
    }
}
