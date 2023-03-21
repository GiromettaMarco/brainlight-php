<?php

namespace Brainlight\BrainlightPhp\Compiler;

class Condition
{
    public static function open(string $variable): string
    {
        return '<?php if(isset($' . $variable . ') && $' . $variable . ') { ?>';
    }

    public static function close(): string
    {
        return '<?php } ?>';
    }
}
