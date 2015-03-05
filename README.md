# Midas-Data

[![Latest Version](https://img.shields.io/github/release/chrismichaels84/midas.svg?style=flat-square)](https://github.com/chrismichaels84/midas/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/chrismichaels84/midas/master.svg?style=flat-square)](https://travis-ci.org/chrismichaels84/midas)
[![Coverage Status](https://coveralls.io/repos/chrismichaels84/midas/badge.svg?branch=master)](https://coveralls.io/r/chrismichaels84/midas?branch=master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/74752ec3-3676-4167-a0f0-b17affea9928/big.png)](https://insight.sensiolabs.com/account/widget?project=74752ec3-3676-4167-a0f0-b17affea9928)

Framework-agnostic manager for data processing and querying. Turn raw data into gold.

> According to myth, Midas was a man bestowed with a golden hand that would transform all he touched to gold. Midas-Data does the same for your data sets. Just don't turn your wife to gold.

This package is in the early development stages. Please do not use in production. Api subject to change as we march toward 1.0. See [CONTRIBUTING](CONTRIBUTING.md) to pitch in.

## Goals
  * Ability to load algorithms and equations, and then solve given parameters
  * Ability to filter, validate, and marshal data in an immutable way. (input one structure, output another)
  * Ability to nest and chain algorithms and equations
  * Save and reuse datasets and algorithms
  * Stream data through commands and algorithms
  * Create a DataObject that can save its own version history
  * Use Outputters to format output for CLI, HTTP, Etc

Please see the proposal.md in the `develop` branch for more information.

## Install
Via Composer
``` bash
$ composer require chrismichaels84/midas
```

## Getting Started
``` php
$midas = new Michaels\Midas\Midas();

/** Use Commands **/
$midas->addCommand('touch', function($data, $params) {
    // process data however you like
    $data .= " has been turned to " . $params . " gold!";
    return $data;
});

$result = $midas->touch('my data', 'pure'); // "my data has been turned to pure gold"
```

## Usage and Concepts
### Issue and Manage Commands
A **command** processes your data and returns the result.

You can add commands to midas in one of three ways. First and simplest is as a **closure**.
```php
$midas->addCommand('alias', function($data, $params, $command) {
    // algorithm here that returns a result
});
```
The closure is handed three arguments when its run: `$data`, `$params`, and `$command`. `$data` is the input to be processed and `$params` is a a lone or set of parameters. Both are passed by the user when they issue the command.

When a command is actually executed, Midas turns it into an object that is an instance of `Commands\GenericCommand` which means it comes with some helpers. These helpers are accessed from the `$command` argument. Think of `$command` as `$this`. And you only need to receive it if you want to.

For more complex commands (especially those that may use dependencies), you can add an instance of `Commands\CommandInterface` either by **classname** or an **instantiated object**.

```php
class MyAwesomeCommand implements \Michaels\Midas\Commands\CommandInterface
{
    // This is the only required method
    public function run($data, $params, $command)
    {
        // Just like the closure, process and return results
    }
}

$midas->addCommand('alias', 'Namespace\MyAwesomeCommand');
// or
$midas->addCommand('alias', new MyAwesomeCommand());
```
You may also extend `Commands\GenericCommand` to inherit the helpers that closures get.

Once you have commands added to Midas, you can manage them in a variety of ways.
```php
$midas->getCommand('alias'); // Returns the raw command (closure, object, or classname)
$midas->getAllCommands(); // Returns array of all commands
$midas->fetchCommand('alias'); // Returns the executable command object
$midas->isCommand('alias'); // Has this command been added?
$midas->setCommand('alias', 'new value'); // Adds or overwrites a command
$midas->removeCommand('alias'); // Removes a single command
$midas->clearCommands(); // Yep, removes all commands
```

Now that you have commands added, all you have to do to use them is talk to midas.
```php
$result = $midas->alias($data, $params);
```
It is best practice to make the aliases verbs so you can speak fluently to Midas.
```php
$result = $midas->convert($data, $params);
$result = $midas->filter($data, $params); // etc
```
This is all done with magic methods. There are some reserved words.

### Save and Manage Datasets
You can also save sets of data to be reused. Anytime you have to manage something, the API is the similar as managing commands. In this case, only `fetch()` works differently.
```php
$midas->addData('alias', $data);
$midas->getData('alias');
$midas->getAllData();
$midas->fetchData('alias'); // Returns the data converted to a Data\RawData instance
$midas->isData('alias'); // Has this data been added?
$midas->setData('alias', 'new value'); // Adds or overwrites a data
$midas->removeData('alias'); // Removes a single data
$midas->clearData(); // Yep, removes all data

$midas->data('alias'); // This will return the raw data
$midas->data('alias', 'fetch'|true); // This will return a RawData instance

$midas->addData('friends', ['michael', 'nicole', 'bethany']);
$midas->someCommand($midas->data('friends'));
```

### Ask and Manage Questions
@todo

### Stream and Pipe Data
@todo

### Configure Midas
Midas have several configurable options. You may set them at instantiation or at any point afterward.
  * `reserved_words`: An array of words that cannot be used as aliases for commands.
More options are on the way.

```php
/* Configure at instantiation */
$midas = new Midas(['option' => 'value']);

/* Configure via manager methods */
$midas->config($item, $fallback); // Get a config item or a fallback
$midas->setConfig($item, $value); // Set a config item or an array of items
$midas->getConfig($item, $fallback);
$midas->getAllConfig();
$midas->getDefaultConfig($item, $fallback); // Get a factory shipped config item
```

### Errors and Exceptions
@todo

### Algorithms and Custom Types
Midas is built on algorithms, which is a function that takes some data and (optionally) parameters, and returns a response (such as refined data). Everything in Midas is a type of algorithm.

All commands and questions are Custom Algorithm Types. You can also create your own Custom Algorithm types. More on this later.
@todo

There are three ways to create algorithms as a closure or through a class. A closure is passed to arguments, the data and whatever parameters. A class must implement whichever interface the algorithm type demands. For instance, commands implement `CommandInterface`. Read about the different types of algorithms like commands and questions.

## Testing
``` bash
$ phpunit
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
