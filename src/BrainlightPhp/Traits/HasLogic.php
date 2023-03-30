<?php

namespace Brainlight\BrainlightPhp\Traits;

trait HasLogic
{
    protected string $logicNamespace;

    protected function resolveLogicNamespace(string $class): string
    {
        $classNamespace = $this->resolveDotsLogic($class);

        if ($this->logicNamespace) {
            $classNamespace = $this->logicNamespace . $classNamespace;
        }

        return $classNamespace;
    }

    protected function resolveDotsLogic(string $class)
    {
        $namespace = '';

        $names = explode('.', $class);

        foreach ($names as $key => $name) {
            if ($key === array_key_last($names)) {
                $namespace .= '\\' . $this->resolveClassName($name);
            } else {
                $namespace .= '\\' . ucfirst($name);
            }
        }

        return $namespace;
    }

    protected function resolveClassName(string $raw)
    {
        $className = '';

        $words = explode('-', $raw);

        foreach ($words as $word) {
            $className .= ucfirst($word);
        }

        return $className;
    }
}
