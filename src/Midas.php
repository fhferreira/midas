<?php
namespace Michaels\Midas;

use Closure;
use Michaels\Midas\Commands\CommandInterface;
use Michaels\Midas\Commands\Manager as CommandManager;
use Michaels\Midas\Data\Manager as DataManager;
use Michaels\Midas\Data\RefinedData;

/**
 * Primary API and entry point
 *
 * @package Michaels\Midas
 */
class Midas
{
    /** @var CommandManager **/
    protected $commands;

    /** @var DataManager **/
    protected $data;

    /**
     * Default configuration
     * @var array
     */
    protected $defaultConfig = [
        'reserved_words' => [
            'is', 'does', 'operation', 'command', 'algorithm', 'data', 'parameter', 'midas',
            'stream', 'pipe', 'end', 'result', 'out', 'output', 'finish', 'solve', 'process', 'solveFor'
        ],
        'test_dummy' => true
    ];

    /**
     * Create a new Midas Instance
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->commands = new CommandManager();
        $this->data = new DataManager();
        $this->config = new Manager(array_merge($this->defaultConfig, $config));
    }

    /**
     * Get a config item
     *
     * @param $item
     * @param null $fallback
     * @return mixed|null
     */
    public function config($item, $fallback = null)
    {
        return $this->config->exists($item) ? $this->config->get($item) : $fallback;
    }

    /**
     * Set a config item or overwrite all config items
     *
     * @param array|string $item
     * @param mixed $value
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

    /**
     * Get a config item or fallback
     *
     * @param string $item
     * @param mixed|null $fallback
     * @return mixed|null
     */
    public function getConfig($item, $fallback = null)
    {
        return $this->config($item, $fallback);
    }

    /**
     * Returns all config items
     *
     * @return array|null
     */
    public function getAllConfig()
    {
        return $this->config->getAll();
    }

    /**
     * Returns a factory default config item
     *
     * @param string $item
     * @param mixed|null $fallback
     * @return mixed|null
     */
    public function getDefaultConfig($item, $fallback = null)
    {
        return isset($this->defaultConfig[$item]) ? $this->defaultConfig[$item] : $fallback;
    }

    /**
     * Add a command to the Command Manager
     *
     * @param string $alias
     * @param string|Closure|CommandInterface $command
     */
    public function addCommand($alias, $command)
    {
        if (in_array($alias, $this->config('reserved_words'))) {
            throw new \InvalidArgumentException("`$alias` is a reserved word");
        }

        $this->commands->add($alias, $command);
    }

    /**
     * Add multiple commands to the Command Manager
     *
     * @param array $commands
     */
    public function addCommands(array $commands)
    {
        $this->commands->add($commands);
    }

    /**
     * Return a raw command from the Command Manager
     *
     * @param string $alias
     * @return mixed
     */
    public function getCommand($alias)
    {
        return $this->commands->get($alias);
    }

    /**
     * Return all raw commands from the Command Manager
     *
     * @return mixed
     */
    public function getAllCommands()
    {
        return $this->commands->getAll();
    }

    /**
     * Add or overwrite a command
     *
     * @param string $alias
     * @param string|Closure|CommandInterface $command
     */
    public function setCommand($alias, $command)
    {
        $this->commands->set($alias, $command);
    }

    /**
     * Remove a single command
     *
     * @param string $alias
     */
    public function removeCommand($alias)
    {
        $this->commands->remove($alias);
    }

    /**
     * Remove all commands
     */
    public function clearCommands()
    {
        $this->commands->clear();
    }

    /**
     * Check for the existence of a command
     *
     * @param string $alias
     * @return bool
     */
    public function isCommand($alias)
    {
        return $this->commands->exists($alias);
    }

    /**
     * Return a command as an instance of CommandInterface
     *
     * @param $alias
     * @return Algorithms\CommandInterface
     */
    public function fetchCommand($alias)
    {
        return $this->commands->fetch($alias);
    }

    /**
     * Add a data set
     *
     * @param string $alias
     * @param mixed|null $data
     */
    public function addData($alias, $data = null)
    {
        $this->data->add($alias, $data);
    }

    /**
     * Return a raw piece of data
     *
     * @param string $alias
     * @return mixed
     */
    public function getData($alias)
    {
        return $this->data->get($alias);
    }

    /**
     * Return all data sets
     *
     * @return array|null
     */
    public function getAllData()
    {
        return $this->data->getAll();
    }

    /**
     * Create or update a data set
     *
     * @param string $alias
     * @param mixed $data
     */
    public function setData($alias, $data)
    {
        $this->data->set($alias, $data);
    }

    /**
     * Remove a single data set
     *
     * @param string $alias
     */
    public function removeData($alias)
    {
        $this->data->remove($alias);
    }

    /**
     * Clear all data sets
     */
    public function clearData()
    {
        $this->data->clear();
    }

    /**
     * Check for existence of a data set
     *
     * @param string $alias
     * @return bool
     */
    public function isData($alias)
    {
        return $this->data->exists($alias);
    }

    /**
     * Return data as a RawData object
     *
     * @param $alias
     * @return Data\RawData
     */
    public function fetchData($alias)
    {
        return $this->data->fetch($alias);
    }

    /**
     * Return saved data set
     * @param string $alias
     * @param bool $fetch
     * @return mixed|Data\RawData
     */
    public function data($alias, $fetch = false)
    {
        if ($fetch) {
            return $this->fetchData($alias);
        }

        return $this->data->get($alias);
    }

    /**
     * Handle commands issued
     *
     * This magic method processes commands.
     *
     * @param string $command
     * @param array $arguments
     * @return RefinedData
     */
    public function __call($command, $arguments)
    {
        if ($command === "run") {
            $command = array_shift($arguments);

        } elseif ($this->currentCommandNs) {
            $command = $this->currentCommandNs . '.' . $command;
        }

        return $this->issueCommand($command, $arguments);
    }

    public function __get($name)
    {
        $dot = ($this->currentCommandNs === false) ? '' : '.';
        $this->currentCommandNs .= $dot . $name;

        return $this;
    }

    protected $currentCommandNs = false;

    /**
     * Ensure that returned data is an instance of RefinedData
     *
     * @param $data
     * @param $returnRefined
     * @return RefinedData
     */
    private function ensureDataCollection($data, $returnRefined)
    {
        if (is_array($data) && $returnRefined) {
            return new RefinedData($data);
        } else {
            return $data;
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return RefinedData
     */
    protected function issueCommand($name, $arguments)
    {
        $command = $this->commands->fetch($name);
        $data = isset($arguments[0]) ? $arguments[0] : null;
        $params = (isset($arguments[1])) ? $arguments[1] : null;
        $returnRefined = (isset($arguments[2])) ? $arguments[2] : true;

        $result = $command->run($data, $params);

        $this->currentCommandNs = false; // reset the command namespace

        return $this->ensureDataCollection($result, $returnRefined);
    }
}
