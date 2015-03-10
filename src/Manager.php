<?php
namespace Michaels\Midas;

use ArrayAccess;

class Manager implements ArrayAccess
{
    /**
     * Arrayable items
     * @var array
     */
    protected $items = [];

    /**
     * Instantiate the Manager with configuration
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * Add an item to the manager
     * @param $alias
     * @param null $algorithm
     * @return $this
     */
    public function add($alias, $algorithm = null)
    {
        // Multiple adds
        if (is_array($alias)) {
            foreach ($alias as $key => $value) {
                $this->add($key, $value);
            }
            return $this;
        }

        // Namespaced
        if (strpos($alias, ".")) {
            $loc = &$this->items;
            foreach (explode('.', $alias) as $step) {
                $loc = &$loc[$step];
            }
            $loc = $algorithm;

            // Singular
        } else {
            $this->items[$alias] = $algorithm;
        }

        return $this;
    }

    /**
     * Get an item from the manager
     * @param $alias
     * @return array|bool
     */
    public function get($alias)
    {
        // Namespaced
        if (strpos($alias, ".")) {
            return $this->findNamespace($alias, $this->items);
        }

        // Non-namespaced, doesn't exist
        if (!isset($this->items[$alias])) {
            return false;
        }

        // Non-namespaced, does exist
        return $this->items[$alias];
    }

    /**
     * Get all the items from the manager
     * @return mixed
     */
    public function getAll()
    {
        return $this->items;
    }

    /**
     * Create or overwrite an item
     * @param $alias
     * @param $value
     */
    public function set($alias, $value)
    {
        $this->items[$alias] = $value;
    }

    /**
     * Overwrite all items with an array
     * @param array $items
     */
    public function reset(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Clear all items from the manager
     * @return $this
     */
    public function clear()
    {
        $this->items = [];
        return $this;
    }

    /**
     * Delete an individual item
     * @param $alias
     */
    public function remove($alias)
    {
        if (isset($this->items[$alias])) {
            unset($this->items[$alias]);
        }
    }

    /**
     * Check if an item exists in the manager
     * @param $alias
     * @return bool
     */
    public function exists($alias)
    {
        if (strpos($alias, ".")) {
            return (bool) $this->findNamespace($alias, $this->items);
        }

        return (isset($this->items[$alias]));
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @param $chain
     * @param $loc
     * @return array|bool
     */
    protected function findNamespace($chain, &$loc)
    {
        foreach (explode('.', $chain) as $step) {
            if (isset($loc[$step])) {
                $loc = &$loc[$step];
            } else {
                return false;
            }
        }
        return $loc;
    }
}