<?php

namespace Brainlight\BrainlightPhp\Compiler;

class Variable
{
    public static function print(string $statement, bool $escape = true): string
    {
        if (preg_match('/^([a-zA-Z0-9_]+)(?:\s*>\s*([a-zA-Z0-9_]+))?$/', $statement, $matches)) {

            if (isset($matches[2])) {
                return static::printWithContext($matches[1], $matches[2], $escape);
            }

            return static::printSimple($matches[1], $escape);
        }

        throw new \Exception("Template syntax error. Tag: {{}}. Statement: '$statement'");
    }

    protected static function printSimple(string $variable, bool $escape = true): string
    {
        if ($escape) {
            return '<?php echo $__brain->escape($' . $variable . '); ?>';
        } else {
            return "<?php echo $$variable; ?>";
        }
    }

    protected static function printWithContext(string $variable, string $context, bool $escape = true): string
    {
        if ($escape) {
            return '<?php echo $__brain->escape(is_array($' . $context . ') ? $' . $context . '[' . $variable . '] : $' . $context . '->' . $variable . '); ?>';
        } else {
            return '<?php echo is_array($' . $context . ') ? $' . $context . '[' . $variable . '] : $' . $context . '->' . $variable . '; ?>';
        }
    }
}