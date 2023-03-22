<?php

namespace Brainlight\BrainlightPhp\Compiler;

class Context
{
    protected static string $regex = '/^([a-zA-Z0-9_]+)(?:\s*>\s*([a-zA-Z0-9_>\s]+))?$/';

    public string $compiled = '';
    public string $variable = '';
    public string $chain = '';
    protected bool $isset;

    public function __construct(string $statement, bool $isset = false)
    {
        $this->isset = $isset;

        $this->run($statement);
    }

    protected function run($statement): void
    {
        if (preg_match(static::$regex, $statement, $matches)) {

            $this->variable = "$$matches[1]";

            if (isset($matches[2])) {
                $this->chain = $this->compileChain($matches[2]);
            }

            $this->compiled = '$__brain->contextualize(' . $this->variable . ', ' . $this->chain;
            if ($this->isset) {
                $this->compiled .= ', true';
            }
            $this->compiled .= ')';

        }
    }

    protected function compileChain(string $statement): string
    {
        $chain = '[';

        $children = preg_split('/>/', $statement, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($children as $key => $child) {
            $chain .= "'" . trim($child) . "'";

            if ($key !== array_key_last($children)) {
                $chain .= ', ';
            }
        }

        return $chain .= ']';
    }

    public static function compile(string $statement, bool $isset = false): string
    {
        return (new Context($statement, $isset))->compiled;
    }
}
