<?php

namespace Brainlight\BrainlightPhp\Compilers\Tags;

use Brainlight\BrainlightPhp\Compilers\Context;

class Escaped extends SingleTag
{
    public static function compile(string $statement): string
    {
        $context = Context::compile($statement);

        if ($context !== '') {
            return '<?php echo $__brain->print(' . $context . '); ?>';
        }

        return '';
    }
}