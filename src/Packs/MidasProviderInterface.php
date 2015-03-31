<?php
namespace Michaels\Midas\Packs;

interface MidasProviderInterface
{
    /**
     * Returns a manifest of algorithms for this pack
     * @return array
     */
    public static function provides();
}
