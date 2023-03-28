<?php

namespace Brainlight\BrainlightPhp\Compilers\Tags;

abstract class SingleTag extends Tag
{
    abstract public static function compile(string $statement): string;
}