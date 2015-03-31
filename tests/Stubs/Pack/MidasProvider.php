<?php
namespace Michaels\Midas\Test\Stubs\Pack;

use Michaels\Midas\Packs\MidasProviderInterface;

class MidasProvider implements MidasProviderInterface
{
    /**
     * Returns a manifest of algorithms provided
     * @return array
     */
    public static function provides()
    {
        // Note that the namespaces will be prefixed by THIS namespace
        return [
            'commands' => [
                'command1' => 'Command\One',
                'command2' => 'Command\Two'
            ]
        ];
    }
}
