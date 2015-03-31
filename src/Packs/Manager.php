<?php
namespace Michaels\Midas\Packs;

use Michaels\Midas\Midas;

/**
 * Manages Packs (basic CRUD)
 * @package Michaels\Midas\Packs
 */
class Manager
{
    private $fromFlag;
    /**
     * @var Midas
     */
    private $midas;

    public function __construct(Midas $midas)
    {
        $this->midas = $midas;
    }

    public function add($pack, $namespace = false)
    {
        // Are we adding a direct array?
        if (is_array($pack) && $namespace) {
            return $this->addFromArray($pack, $namespace);
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
        return $this->addFromArray($manifest, $namespace);
    }

    /**
     * @param $pack
     * @param $namespace
     * @return bool
     */
    protected function addFromArray($pack, $namespace)
    {
        foreach ($pack as $type => $manifest) {
            foreach ($manifest as $alias => $algorithm) {
                $this->midas->addCommand($namespace . "." . $alias, $algorithm);
            }
        }

        return true;
    }

    public function addFromPack($type, $alias)
    {
        $this->fromFlag = ['type' => $type, 'alias' => $alias];
        return $this;
    }

    public function from($pack)
    {
        // $midas->addCommand('alias')->from('vendor.pack');
        $this->midas->addCommand(
            $pack . "." . $this->fromFlag['alias'],
            $this->getFromProvider($pack, $this->fromFlag['type'], $this->fromFlag['alias'])
        );
    }

    private function getFromProvider($pack, $type, $alias)
    {
        $manifest = $this->getManifest($pack);
        return $manifest[$type][$alias];
    }

    /**
     * @param $pack
     * @return mixed
     */
    protected function getManifest($pack)
    {
        // Create the Class Namespace
        $pieces = explode(".", $pack);
        array_walk($pieces, function (&$piece) {
            $piece = ucfirst($piece);
        });
        $classNamespace = implode("\\", $pieces);

        // Get from provider
        $provider = $classNamespace . "\\MidasProvider";
        /** @noinspection PhpUndefinedMethodInspection */
        $pack = $provider::provides();
        return $pack;
    }
}
