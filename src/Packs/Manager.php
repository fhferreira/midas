<?php
namespace Michaels\Midas\Packs;

use Michaels\Midas\Midas;

/**
 * Manages Algorithm Packs
 *
 * @package Michaels\Midas\Packs
 */
class Manager
{
    /**
     * Holds from() method-chain data
     * @var array
     */
    private $fromFlag;

    /**
     * The Midas instance
     * @var Midas
     */
    private $midas;

    /**
     * Builds a new Pack Manager
     *
     * @param Midas $midas
     */
    public function __construct(Midas $midas)
    {
        $this->midas = $midas;
    }

    /**
     * Adds all algorithms from a pack to Midas
     *
     * Optionally, you can designate a destination namespace
     *
     * @param string $pack Psr-4 vendor namespace in dot notation (vendor.pack)
     * @param bool $namespace Optional destination namespace in dot notation
     * @return Manager $this
     */
    public function add($pack, $namespace = false)
    {
        // Are we adding a direct array?
        if (is_array($pack) && $namespace) {
            $this->addFromArray($pack, $namespace);
            return $this;
        }

        // No, so we are adding algorithms from a vendor.pack
        $namespace = $pack;
        $manifest = $this->getManifest($pack);

        // Prefix each algorithm with the correct namespace
        foreach ($manifest as $type => $list) {
            foreach ($list as $alias => $algorithm) {
                $list[$alias] = $namespace.".".$list[$alias];
            }
        }

        // Add the commands
        $this->addFromArray($manifest, $namespace);
        return $this;
    }

    /**
     * Sets the from flag with the type and alias
     *
     * `$midas->addCommand($alias)->from('vendor.pack')`
     *
     * @param string $type Type of algorithm
     * @param string $alias
     * @return $this
     */
    public function addFromPack($type, $alias)
    {
        $this->fromFlag = ['type' => $type, 'alias' => $alias];
        return $this;
    }

    /**
     * Sets the from flag with the type. Used for multiple.
     *
     * `$midas->addCommands()->from('vendor.pack')`
     *
     * @param string $type
     * @return $this
     */
    public function addTypeFromPack($type)
    {
        $this->fromFlag = ['type' => $type, 'alias' => false];
        return $this;
    }

    /**
     * Generates an array of algorithms from a specific pack based on $fromFlag
     * then adds them to the Midas Instance
     *
     * @param string $pack PSR-4 Namespace in dot notation
     */
    public function from($pack)
    {
        // Set parameters
        $type = $this->fromFlag['type'];
        $alias = $this->fromFlag['alias'];
        $namespace = (isset($this->fromFlag['namespace'])) ?
            $this->fromFlag['namespace'] : $pack;

        if ($alias === false) {
            // We want all the algorithms of this type
            $manifest[$type] = $this->getFromProvider($pack, $type, $alias);

        } else {
            // We want a specific command
            $manifest[$type][$alias] = $this->getFromProvider($pack, $type, $alias);
        }

        // Add the commands
        $this->addFromArray($manifest, $namespace);

        // Reset the fromFlag
        $this->fromFlag = null;
    }

    /**
     * Set from flag destination namespace to custom
     *
     * @param bool $namespace
     * @return $this
     */
    public function under($namespace = false)
    {
        $this->fromFlag['namespace'] = $namespace;
        return $this;
    }

    /**
     * Set from flag destination namespace to top-level
     *
     * @return $this
     */
    public function toTop()
    {
        return $this->under(false);
    }

    /**
     * Adds algorithms to Midas instance from a manifest array
     *
     * @param array $manifest ['type' => ['alias' => algorithm]]
     * @param string|bool $namespace
     */
    private function addFromArray(array $manifest, $namespace = false)
    {
        $prefix = ($namespace === false) ? '' : $namespace . ".";

        foreach ($manifest as $type => $list) {
            foreach ($list as $alias => $algorithm) {
                $this->midas->addCommand($prefix . $alias, $algorithm);
            }
        }
    }

    /**
     * Returns single algorithm or manifest of algorithms of a certain type
     *
     * @param string $pack Namespace of algorithm pack in dot notation
     * @param string $type Type of algorithm desired
     * @param string|bool $alias Name of specific algorithm to fetch
     * @return array|string
     */
    private function getFromProvider($pack, $type, $alias = false)
    {
        $manifest = $this->getManifest($pack);

        // Return a single algorithm
        if ($alias !== false) {
            return $manifest[$type][$alias];
        }

        // Return all algorithms of a certain type
        return $manifest[$type];
    }

    /**
     * Retrieves a full manifest from a pack's MidasProvider
     *
     * @param string $pack PSR-4 Namespace in dot notation
     * @return mixed
     */
    protected function getManifest($pack)
    {
        // Create the Class Namespace
        $pieces = explode(".", $pack);
        array_walk($pieces, function (&$piece) {
            $piece = ucfirst($piece);
        });
        $provider = implode("\\", $pieces) . "\\MidasProvider";

        // Retrieve manifest from provider
        /** @noinspection PhpUndefinedMethodInspection */
        $manifest = $provider::provides();

        return $manifest;
    }
}
