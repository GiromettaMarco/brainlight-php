<?php

namespace Brainlight\BrainlightPhp\Types;

class Slot
{
    public string $name;
    public string $content;
    public int $level;

    public function __construct(string $name, string $content, int $level = 0)
    {
        $this->name = $name;
        $this->content = $content;
        $this->level = $level;
    }

    public function hasLevel(int $level): bool
    {
        if ($this->level === $level) {
            return true;
        }

        return false;
    }
}
