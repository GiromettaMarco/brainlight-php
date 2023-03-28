<?php

namespace Brainlight\BrainlightPhp\Compilers\Assignments;

class Shorthand extends Assignment
{
    protected static string $regex = '/^:([a-zA-Z0-9_]+)$/';

    protected static function makeVariable(array $matches): string
    {
        return "'$matches[1]' => \$$matches[1]";
    }
}
