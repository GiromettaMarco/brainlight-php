<?php

namespace Brainlight\BrainlightPhp;

abstract class Logic
{
    public ?string $template;

    protected array $mandatory = [];

    protected array $mandatorySlots = [];

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
                $this->throwError("Missing argument '$mandatory'");
            }
        }
    }

    public function filterVariables(array $parameters): array
    {
        $this->checkParameters($parameters);

        return $this->getVariables($parameters);
    }

    public function checkSlots(array $slotNames): void
    {
        foreach ($this->mandatorySlots as $mandatory) {
            if (! in_array($mandatory, $slotNames, true)) {
                $this->throwError("Missing slot '$mandatory'");
            }
        }
    }

    protected function throwError(?string $message = null): void
    {
        $error = 'Runtime error.';

        if (isset($this->template)) {
            $error .= " Template: $this->template.";
        }

        if (isset($message)) {
            $error .= " $message";
        }

        throw new \InvalidArgumentException($error);
    }
}

