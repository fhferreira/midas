### Manage Entities
An entity is a command, algorithm, dataset, or question. The API for managing these are nearly identical.

```php
$midas = new Midas();

/* Add Entities */
$midas->addAlgorithm('alias', 'Class\Name'); // $midas->addAl();
$midas->getAlgorithm('solve');
$midas->isAlgorithm(); 
$midas->setAlgorithm(); 
$midas->removeAlgorithm();
$midas->clearAlgorithms():
```