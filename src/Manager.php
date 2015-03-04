<?php
namespace Michaels\Midas;

use ArrayAccess;

class Manager implements ArrayAccess
{
    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $items;
    }

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

    public function get($alias)
    {
        // Namespaced
        if (strpos($alias, ".")) {
            $loc = &$this->items;
            foreach (explode('.', $alias) as $step) {
                if (isset($loc[$step])) {
                    $loc = &$loc[$step];
                } else {
                    return false;
                }
            }
            return $loc;
        }

        // Non-namespaced, doesn't exist
        if (!isset($this->items[$alias])) {
            return false;
        }

        // Non-namespaced, does exist
        return $this->items[$alias];
    }

    public function exists($alias)
    {
        return (isset($this->items[$alias]));
    }

    public function clear()
    {
        $this->items = [];
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->items;
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

    public function set($alias, $value)
    {
        $this->items[$alias] = $value;
    }

    public function remove($alias)
    {
        if (isset($this->items[$alias])) {
            unset($this->items[$alias]);
        }
    }

    public function reset(array $items = [])
    {
        $this->items = $items;
    }
}
