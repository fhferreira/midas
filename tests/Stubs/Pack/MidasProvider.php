<?php
namespace Michaels\Midas\Test\Stubs\Pack;

class MidasProvider
{
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
