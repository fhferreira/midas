<?php
namespace Michaels\Midas\Commands;

use Closure;

/**
 * A generic command used to wrap closure-based commands
 *
 * @package Michaels\Midas\Commands
 */
class GenericCommand implements CommandInterface
{
    /**
     * Stored algorithm if command converted from closure
     * @var
     */
    protected $closure;

    /**
     * Convert a closure to a GenericCommand
     *
     * @param callable $closure
     * @return static
     */
    public static function create(Closure $closure)
    {
        $algorithm = new static;
        $algorithm->setRun($closure);

        return $algorithm;
    }

    /**
     * @inheritdoc
     */
    public function run($data, $params = null)
    {
        $closure = $this->closure;
        $result = $closure($data, $params, $this);

        return $result;
    }

    /**
     * Save a closure
     *
     * @param $closure
     */
    private function setRun($closure)
    {
        $this->closure = $closure;
    }
}
