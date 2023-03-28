<?php

namespace Brainlight\BrainlightPhp\Compilers;

class Slot
{
    public static function open(string $slotName): string
    {
        return '<?php $__brain->startSlot(\'' . $slotName . '\'); ?>';
    }

    public static function close(): string
    {
        return '<?php $__brain->stopSlot(); ?>';
    }
}
