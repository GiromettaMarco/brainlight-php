<?php

namespace Brainlight\BrainlightPhp\Compilers\Assignments;

abstract class Assignment
{
    protected static string $regex;

    abstract protected static function makeVariable(array $matches): string;

    public static function compile(string $token, bool $last = false, ?string &$output = null): bool
    {
        $compiled = '';

        if (preg_match(static::$regex, $token, $matches)) {

            $compiled .= static::makeVariable($matches);

            if ( ! $last) {
                $compiled .= ', ';
            }

            if (isset($output)) {
                $output .= $compiled;
            }
        }

        return $compiled;
    }
}
