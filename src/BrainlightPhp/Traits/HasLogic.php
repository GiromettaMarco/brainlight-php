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
        $names = explode('.', $class);
        $namespace = '';
        foreach ($names as $name) {
            $namespace .= '\\' . ucfirst($name);
        }
        return $namespace;
    }
}
