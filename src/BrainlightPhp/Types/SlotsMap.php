<?php

namespace Brainlight\BrainlightPhp\Types;

use Brainlight\BrainlightPhp\Types\Slot;

class SlotsMap
{
    public array $slots;
    public int $level;

    public function __construct(array $slots = [])
    {
        $this->slots = $slots;
        $this->level = 0;
    }

    public function make(string $name, string $content): Slot
    {
        return $this->slots[] = new Slot($name, $content, $this->level);
    }

    public function increaseLevel(): int
    {
        return $this->level++;
    }

    public function decreaseLevel(): int
    {
        return $this->level--;
    }

    public function pop(): array
    {
        $collected = [];

        foreach($this->slots as $key => $slot) {
            if ($slot->hasLevel($this->level)) {
                $collected[$slot->name] = $slot->content;
                unset($this->slots[$key]);
            }
        }

        $this->slots = array_values($this->slots);

        return $collected;
    }
}
