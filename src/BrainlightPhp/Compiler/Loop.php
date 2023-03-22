<?php

namespace Brainlight\BrainlightPhp\Compiler;

class Loop
{
    protected static string $regex = '/^([a-zA-Z0-9_>\s]+?)(?:\s+@\s+([a-zA-Z0-9_]+)(?:\s*>\s*([a-zA-Z0-9_]+))?)?$/';

    public static function open(string $statement): string
    {
        if (preg_match(static::$regex, $statement, $matches)) {

            if (isset($matches[2])) {
                return static::foreach(Context::compile($matches[1]), $matches[2], $matches[3] ?? '');
            }

            return static::for(Context::compile($matches[1]));
        }

        throw new \Exception("Template syntax error. Tag: {{#}}. Statement: '$statement'");
    }

    public static function close(): string
    {
        return '<?php } ?>';
    }

    protected static function for(string $max): string
    {
        return '<?php for ($index = 0; $index < intval(' . $max . '); $index++) { ?>';
    }

    protected static function foreach(string $array, string $first, string $last = ''): string
    {
        $compiled = '<?php foreach(' . $array . ' as $' . $first;
        if ($last !== '') {
            $compiled .= ' => $' . $last;
        }
        return $compiled . ') { ?>';
    }
}