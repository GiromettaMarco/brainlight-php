<?php

namespace Brainlight\BrainlightPhp;

abstract class Logic
{
    public ?string $template;

    protected array $mandatory = [];

    public function __construct(string $template)
    {
        if (! isset($this->template)) {
            $this->template = $template;
        }
    }

    abstract protected function getVariables(array $parameters): array;

    protected function checkParameters(array $parameters): void
    {
        foreach ($this->mandatory as $mandatory) {
            if (! isset($parameters[$mandatory])) {
                throw new \InvalidArgumentException("Missing argument '$mandatory'");
            }
        }
    }

    public function filterVariables(array $parameters): array
    {
        $this->checkParameters($parameters);

        return $this->getVariables($parameters);
    }
}

