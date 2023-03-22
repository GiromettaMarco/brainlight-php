<?php

namespace Brainlight\BrainlightPhp\Compiler;

class Condition
{
    public static function open(string $statement): string
    {
        $context = new Context($statement, true);
        return '<?php if(isset(' . $context->variable . ') && ' . $context->compiled . ') { ?>';
    }

    public static function close(): string
    {
        return '<?php } ?>';
    }
}
