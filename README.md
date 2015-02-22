# Midas-Data
Framework-agnostic manager for algorithms, equations, and data processing tasks. Turn raw data into gold.

*According to myth, Midas was a man bestowed with a golden hand that would transform all he touched to gold. Midas-Data does the same for your data sets. Just don't turn your wife to gold.*

This package is in the very early proposal stages. There is no actionable code as of yet. Please issue a pull request against this README.md to make suggestions.

## Goals
  * Ability to load algorithms and equations, and then solve given parameters
  * Ability to filter, validate, and marshal data in an immutable way. (input one structure, output another)
  * Ability to nest and chain algorithms and equations
  * Save and reuse datasets and algorithms
  * Stream data through commands and algorithms
  * Create a DataObject that can save its own version history
  * Use Outputters to format output for CLI, HTTP, Etc

## Use Cases
When would it be good to use Midas?

## Introduction
Midas is a processing object that works on whatever data you provide it. You can ask *questions* of that data or issue *commands* to process that data in some way. The Midas package itself is almost empty. That is because all the questions and commands are algorithms that you load into (or teach to) midas. You can also save data sets to be reused, use data in mutable or immutable ways, nest algorithms and data, and stream data through multiple algorithms, outputting it in any way you choose.

## Concepts
These are the basic terms and concepts that make midas work.

A **command** processes your data according to whichever algorithm the command is tied to and then returns a RefinedDataObject with the results.

A **question** analyzed your data according to whichever algorithm the question is tied to and returns `true` or `false`. There may be the ability for questions in the future which return more complex answers.

A **stream** or **pipe** is an ordered sequence or algorithms which your data is processed through, finally returning a RefinedDataObject or outputing the data in some way.

#### Objects and Classes
A **Midas** object contains and monages algorithms, commands, pipes, and config. This is the public API.

A **MidasDataObject** is a Midas Object that can save data results to itself

**Algorithms** process raw data and return a refined DataObject.

**Commands** are algorithms that can be issued from `$midas` directly.

A **pipe** or **stream** allows for chaining algorithms together.

An **Outputer** outputs the data from a pipe or stream without stopping the flow

The **Algorithm Manager** and **CommandManager** are protected sub-objects of Midas that manage algorithms and commands.


## Sample API
```php
$midas = new Midas($config);

/* Use algorithms */
$result = $midas->process($data, 'algorithm', $params);
$result = $midas->process($data, function(RawData $data){}, $params);
$result = $midas->process($data, 'Namespace\Algorithm', $params);

/* Use Commands */
$result = $midas->solve($data, $params); // magic method
$result = $midas->filter($data, $criteria);

/* Ask Questions */
$answer = $midas->is($data, $question, $params);
$answer = $midas->is($data)->question($parames);

$data = $midas->make($data);
$data->isQuestion($params);

// Chain questions with conjunctions
$midas->is($data)
 ->question1($params)
 ->and()->question2()
 ->or()->question3()
 ->butNot()->question4();
 
 // Finally, you can use closures to order comparrisons
 $midas->is($data)
  ->opperation(function($a){
   return $a->question1($params)->and()->question2();
  })->butNot()->opperation(function($a){
   return $a->question3($params)->or()->question4();
  });

/* Manage commands */
$midas->addCommand('solve', new EquationCommand());
$midas->addCommand('solve', 'Namespace\EquationCommand');
$midas->addCommand('solve', function(RawData $data){
  return $processedData;
});

// Or use an IoC container to resolve Command Dependencies
$container = Container; // PHP
$container->add('Dependency');
$container->add('solver', 'SolverCommand')
          ->withArgument('Dependency')
          ->withArgument($someConfig);
          
$midas->addCommand('solve', $container->get('solver'));

// Getters, Setters, and Helpers
$midas->getCommand('solve');
$midas->extendCommand('solve', 'balance', 'balance'); // solve.balance -> EquationCommand::balance()
$midas->extendCommand('solve', 'balance', 'other ways of registering commands');
$midas->isCommand(); $midas->setCommand(); $midas->deleteCommand();
$midas->clearCommands():

/* Same API for Managing Algorithms */

/* Save Data Sets for reuse */
$midas->addData('dataset', $data);
$midas->solve('dataset', $params);
$midas->process('dataset', $params);

$dataset = $midas->getDataSet('dataset'); // Returns MidasDataObject
$dataset->solve($params);

$dataset = $midas->getDataSet('dataset', false); // Returns a ResultDataSet, not methods

/* Streaming and Pipes */
$midas->stream($data, [
  ['command', $params],
  ['command', $params],
  [':algorithm', $params]
]);

// Streams go through algorithms, unless specified as command
$midas->stream($data) // or ->pipe($data)
 ->through()
   ->command('name', $params)
   ->algorithm($params)
   ->out(new Outputter()) // default echoes, otherwise goes through Outputter
   ->algorithm($params)
   ->command('name', $params)
   ->return(); // Ends the stream an returns refined data

/** Optionally, you can create a MidasDataObject **/
// Saves all stages to the data object
$data = $midas->newDataObject() // or data($data) or make($data)
$data->set($data) // if using data() or make(), you can skip this
$data->command(); // 0
$data->process('algorithm') //1
$data->stream()->through()->algorithm()->end(); // 2

// Now you can get it
$zero = $data->getResult(0) // getFirstResult()
$one = $data->getResult(1) // getResult(2)->getPreviousResult()->getNextResult()
$two = $data->getResult(2) // getLastResult()
$two = $data->get(); // get's latest result
```
## Compared to Gulp
  * First, gulp runs in php. That's the main difference
  * Becuase of this, Midas is not truly streaming
  * Midas is completely synchronous

## Architecture
  * `Midas\Midas`: Main Midas Class
  * `Midas\MidasData`: extends Midas, has Data trait
  * `Midas\Data`: olds data, both Raw and Refined
  * `Midas\Pipe`: a streamer
  * `Midas\CommandManager`: manages Commands
  * `Midas\AlgorithmManager`: manages algorithms
  * `Midas\ComandInterface`: contract
  * `Midas\AlgorithmInterface`: contract
  * `Midas\ParameterBag`: holds parameters
  * `Midas\Data` extends `Illuminate\Support\Collections`

## Roadmap for the Future
#### v0.1 Midas Container
  * Main Midas Container
  * Manage Commands
  * Manage Algorithms

#### v0.2 Process Data and Return
  * Process through commands and algorithms
  * Create Data Objects that extend Collections
  * Return Refined Data Objects (Not MidasData)
  * **First Release**

#### v0.3 Transform and Equation Commands
  * Wrap Fractal to output data via an algorithm (as a test)
  * Solve equations using PHP math function
 
#### v0.4 Datasets
  * Save datasets for reuse
 
#### v0.5 Streaming A
  * Stream `$data` via an array of commands and algorightms
  * ```php $midas->stream($data, [['command', $params]]);```

#### v0.6 Streaming B
  * Stream `$data` using pipes
  * ```php $midas->stream($data)->through()->algorithm()->return();```
  * Endpoints: `return()`, `end()`, `out()`, `out(Outputter $outputter)`

#### v0.7 Midas Data Objects
  * Create Midas Data Objects for self storage
  * ```php $data = $midas->make($data); $data->command('x');```

#### v0.8 First Party algorithms
  * Marshal() with Aura\Marshal
  * filter() with Aura\Filter
  * validate() with Dependency

#### v1.0 Bugsquash and Awesome
  * Can be released after v0.7 and run paralell with First Party Algorithms

## Potential first-party algorithms/commands
  * `solve()` for equations
  * `marshal()` for conforming data w/ dependency
  * `transform()` for outputting data w/ fractal dependency
  * `solveFor()` for equations
  * `filter()` returning results from dataset w/ dependency
  * `valdate()` returns data schema errors w/ dependency

**Other potential names**: Alchemist, Spinner, Forge, Kiln Cauldron
