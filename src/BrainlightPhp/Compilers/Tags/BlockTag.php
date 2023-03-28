<?php

namespace Brainlight\BrainlightPhp\Compilers\Tags;

abstract class BlockTag extends Tag
{
    abstract public static function open(string $statement): string;

    abstract public static function close(): string;
}