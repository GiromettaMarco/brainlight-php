<?php

namespace Brainlight\BrainlightPhp\Compilers\Tags;

use Brainlight\BrainlightPhp\Compilers\Assignments,
    Brainlight\BrainlightPhp\Types\Extension;

class Inclusion extends SingleTag
{
    protected static string $regex = '/^(\+?)\s*([a-zA-Z0-9_\-.]+)(?:\s+([\S\s]*))?$/';

    public static function compile(string $statement): string
    {
        if (preg_match(static::$regex, $statement, $matches)) {

            $data = (isset($matches[3])) ? Assignments::compile($matches[3]) : '[]';

            $logic = ($matches[1] !== '') ? 'true': 'false';

            return '<?php echo $__brain->includePartial(\'' . $matches[2] . '\', ' . $data . ', ' . $logic . '); ?>';
        }

        trigger_error("Template syntax error; Tag: {{>}}; Statement: '$statement'", E_USER_WARNING);
        return '';
    }

    public static function getExtension(string $statement): Extension
    {
        if (preg_match(static::$regex, $statement, $matches)) {
            return new Extension($matches[2], Assignments::compile($matches[3] ?? ''), $matches[1]);
        }

        trigger_error("Template syntax error; Tag: {{&}}; Statement: '$statement'", E_USER_WARNING);
        return null;
    }
}