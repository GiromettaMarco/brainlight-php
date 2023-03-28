<?php

namespace Brainlight\BrainlightPhp\Compilers\Assignments;

class Constant extends Assignment
{
    protected static string $regex = '/^([a-zA-Z0-9_]+)=([0-9.]*|true|false)$/';

    protected static function makeVariable(array $matches): string
    {
        return "'$matches[1]' => $matches[2]";
    }
}
