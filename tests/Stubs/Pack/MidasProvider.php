<?php
namespace Michaels\Midas\Test\Stubs\Pack;

class MidasProvider
{
    public static function provides()
    {
        return [
            'commands' => [
                'command1' => 'Command\One',
                'command2' => 'Command\Two'
            ]
        ];
    }
}
