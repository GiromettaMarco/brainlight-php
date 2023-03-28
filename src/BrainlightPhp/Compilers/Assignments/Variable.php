<?php

namespace Brainlight\BrainlightPhp\Compilers\Assignments;

use Brainlight\BrainlightPhp\Compilers\Context;

class Variable extends Assignment
{
    protected static string $regex = '/^:([a-zA-Z0-9_]+)=(.+)$/';

    protected static function makeVariable(array $matches): string
    {
        $context = Context::compile($matches[2]);

        if ($context === '') {
            trigger_error("Template syntax error. Invalide assignment: '$matches[0]'", E_USER_WARNING);
            return '';
        }

        return "'$matches[1]' => $context";
    }
}
