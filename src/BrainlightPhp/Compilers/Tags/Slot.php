<?php

namespace Brainlight\BrainlightPhp\Compilers\Tags;

class Slot extends BlockTag
{
    public static function open(string $statement): string
    {
        return '<?php $__brain->startSlot(\'' . $statement . '\'); ?>';
    }

    public static function close(): string
    {
        return '<?php $__brain->stopSlot(); ?>';
    }
}
