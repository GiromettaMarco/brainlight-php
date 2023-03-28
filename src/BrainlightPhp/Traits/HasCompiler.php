<?php

namespace Brainlight\BrainlightPhp\Traits;

use Brainlight\BrainlightPhp\Compilers\Core;

trait HasCompiler
{
    protected function compile(string $filename): string
    {
        return Core::compile(file_get_contents($filename), $filename);
    }
}
