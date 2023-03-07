<?php

namespace Brainlight\BrainlightPhp;

class CacheId
{
    public string $hash;
    public string $time;

    public function __construct(string $filename)
    {
        $this->hash = hash('md5', $filename);
        $this->time = filemtime($filename);
    }

    public function getName(): string
    {
        return $this->hash . '_' . $this->time;
    }
}
