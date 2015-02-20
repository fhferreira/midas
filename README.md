# Midas
Framework-agnostic manager for algorithms, equations, and data processing tasks. Turn raw data into gold.

This package is in the very early proposal stages. There is no actionable code as of yet. Please issue a pull request against this README.md to make suggestions.

## Goals
  * Ability to load algorithms and equations, and then solve given parameters
  * Ability to filter, validate, and marshal data in an immutable way. (input one structure, output another)
  * Ability to nest and chain algorithms and equations
  * Save and reuse datasets and algorithms

## Introduction

## Concepts
These are the basic terms and concepts that make midas work.

A **Midas** object contains and monages algorithms, commands, pipes, and config. This is the public API.

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

/* Manage commands */
$midas->addCommand('solve', new EquationCommand());
$midas->addCommand('solve', 'Namespace\EquationCommand');
$midas->addCommand('solve', function(RawData $data){
  return $processedData;
});

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
  ['command', $params'],
  ['command', $params'],
  [':algorithm', $params']
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
$data = $midas->newDataObject()
$data->set($data)
$data->command(); // 0
$data->process('algorithm') //1
$data->stream()->through()->end(); // 2

// Now you can get it
$zero = $data->getResult(0)
$one = $data->getResult(1)
$two = $data->getResult(2)
$two = $data->get();
```

Other potential names: Alchemist, Spinner, Forge, Kiln
