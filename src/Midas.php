<?php
namespace Michaels\Midas;

use Michaels\Midas\Commands\Manager as CommandManager;
use Michaels\Midas\Data\Manager as DataManager;
use Michaels\Midas\Data\RefinedData;
use Michaels\Midas\Questions\Manager as QuestionManager;

class Midas
{
    protected $commands;
    protected $data;
    protected $defaultConfig = [
        'reserved_words' => [
            'is', 'does', 'opperation', 'command', 'algorithm', 'data', 'parameter', 'midas',
            'stream', 'pipe', 'end', 'result', 'out', 'output', 'finish', 'solve', 'process', 'solveFor'
        ],
        'errors' => 'exceptions', // or silent
        'test_dummy' => true
    ];
//    protected $questions;

    /**
     * Create a new Midas Instance
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->commands = new CommandManager();
//        $this->questions = new QuestionManager();
        $this->data = new DataManager();
        $this->config = new Manager(array_merge($this->defaultConfig, $config));
    }

    /**
     * Get a config item
     * @param $item
     * @param null $fallback
     * @return array|bool|null
     */
    public function config($item, $fallback = null)
    {
        return $this->config->exists($item) ? $this->config->get($item) : $fallback;
    }

    /**
     * Set a config item or overwrite all config items
     * @param $item
     * @param bool $value
     * @return $this
     */
    public function setConfig($item, $value = false)
    {
        if (is_array($item)) {
            $this->config->reset($item);
            return $this;
        }

        $this->config->set($item, $value);

        return $this;
    }

    public function getConfig($item, $fallback = null)
    {
        return $this->config($item, $fallback);
    }

    public function getAllConfig()
    {
        return $this->config->getAll();
    }


    public function getDefaultConfig($item, $fallback = null)
    {
        return isset($this->defaultConfig[$item]) ? $this->defaultConfig[$item] : $fallback;
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
