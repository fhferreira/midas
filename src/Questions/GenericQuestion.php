<?php
namespace Michaels\Midas\Questions;

use Closure;

class GenericQuestion implements QuestionInterface
{

    protected $closure;

    public static function create(Closure $closure)
    {
        $algorithm = new static;
        $algorithm->setRun($closure);

        return $algorithm;
    }

    public function run($data, array $params = null)
    {
        $closure = $this->closure;
        $result = $closure($data, $params);

        return $result;
    }

    private function setRun($closure)
    {
        $this->closure = $closure;
    }
}
