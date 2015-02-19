# Spinner
Framework-agnostic manager for algorithms, equations, and math-related tasks. Spinning raw data into gold.

This package is in the very early proposal stages. There is no actionable code, as of yet. Please issue a pull request against this README.md to make suggestions.

## Goals
  * Ability to load algorithms and equations, and then solve given parameters
  * Ability to filter, validate, and marshal data in an immutable way. (input one structure, output another)
  * Helper math functions
  * Ability to nest and chain algorithms and equations
  * Ability to run algorithms in parallel (async)

## Sample API
```php
$spinner = new Spinner($config);

/* Basic Algorithms */
$spinner->addAl('sum', function(Spinner\Params $parameters) {
   return array_sum($parameters->toArray());
});

$spinner->addDataSet('my_data', new Spinner\Data([1, 5, 99, 2]));
$result = $spinner->solve('sum', 'my_data');
// Spinner\Result {
   $result = 107;
}
```

Other potential names: Alchemist, Midas
