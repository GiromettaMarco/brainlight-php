<?php

namespace Brainlight\BrainlightPhp\Compilers\Assignments;

class Text extends Assignment
{
    protected static string $regex = '/^([a-zA-Z0-9_]+)="([^"]*)"$/';

    protected static function makeVariable(array $matches): string
    {
        return "'$matches[1]' => '" . addslashes($matches[2]) . "'";
    }
}
