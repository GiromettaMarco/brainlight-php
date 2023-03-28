<?php

namespace Brainlight\BrainlightPhp\Compilers\Tags;

use Brainlight\BrainlightPhp\Compilers\Context;

class Condition extends BlockTag
{
    public static function open(string $statement): string
    {
        $context = new Context($statement, true);
        return '<?php if (isset(' . $context->variable . ') && ' . $context->compiled . ') { ?>';
    }

    public static function close(): string
    {
        return '<?php } ?>';
    }
}