<?php

namespace Brainlight\BrainlightPhp\Traits;

use Brainlight\BrainlightPhp\Types\SlotsMap;

trait HasSlots
{
    /**
     * Stack of slot names provided at runtime.
     *
     * @var array
     */
    protected array $slotNames = [];

    /**
     * Container of slots.
     *
     * @var Brainlight\BrainlightPhp\Types\SlotsMap
     */
    protected SlotsMap $slots;

    /**
     * Starts a slot directive.
     *
     * @param string $slotName
     */
    public function startSlot(string $slotName)
    {
        $this->slotNames[] = $slotName;
        ob_start();
    }

    /**
     * Stops a slot directive.
     */
    public function stopSlot()
    {
        $this->slots->make(array_pop($this->slotNames), ob_get_clean());
    }
}
