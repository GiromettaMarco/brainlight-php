<?php

namespace Brainlight\BrainlightPhp\Compiler;

class Loop
{
    public static function open(string $statement): string
    {
        if (preg_match('/^([a-zA-Z0-9_]+)(?:\s+as\s+([a-zA-Z0-9_]+)(?:\s*>\s*([a-zA-Z0-9_]+))?)?$/', $statement, $matches)) {

            if (isset($matches[2])) {
                return static::foreach($matches[1], $matches[2], $matches[3] ?? '');
            }

            return static::for($matches[1]);
        }

        throw new \Exception("Template syntax error. Tag: {{#}}. Statement: '$statement'");
    }

    public static function close(): string
    {
        return '<?php } ?>';
    }

    protected static function for(string $max): string
    {
        return '<?php for ($index = 0; $index < intval($' . $max . '); $index++) { ?>';
    }

    protected static function foreach(string $array, string $first, string $last = ''): string
    {
        $compiled = '<?php foreach($' . $array . ' as $' . $first;
        if ($last !== '') {
            $compiled .= ' => $' . $last;
        }
        return $compiled . ') { ?>';
    }
}