<?php

namespace Brainlight\BrainlightPhp\Compiler;

use Brainlight\BrainlightPhp\Types\Extension;

class Inclusion
{
    protected static string $regex = '/^(\+?)\s*([a-zA-Z0-9_\-.]+)(?:\s+([\S\s]*))?$/';

    public static function partial(string $statement): string
    {
        if (preg_match(static::$regex, $statement, $matches)) {

            $data = (isset($matches[3])) ? Assignments::compile($matches[3]) : '[]';

            $code = '<?php echo $__brain->';

            if ($matches[1] !== '') {
                $code .= 'includePartialWithLogic';
            } else {
                $code .= 'includePartial';
            }

            return $code .= '(\'' . $matches[2] . '\', ' . $data . '); ?>';
        }

        throw new \Exception("Template syntax error. Tag: {{>}}. Statement: '$statement'");
    }

    public static function getExtension(string $statement): Extension
    {
        if (preg_match(static::$regex, $statement, $matches)) {
            return new Extension($matches[2], Assignments::compile($matches[3] ?? ''), $matches[1]);
        }

        throw new \Exception("Template syntax error. Tag: {{&}}. Statement: '$statement'");
    }
}
