<?php

namespace Brainlight\BrainlightPhp\Traits;

trait HasCompiler
{
    protected ?array $extending;

    protected function compile(string $filename): string
    {
        $content = $this->compileSlots(file_get_contents($filename));
        $content = $this->compileExtension($content);
        $content = $this->compileInclusions($content);
        $content = $this->compileConditions($content);
        $content = $this->compileLoops($content);
        $content = $this->compileVariables($content);
        $content .= $this->getExtension();
        $content .= $this->getSource($filename);

        return $content;
    }

    protected function compileVariables(string $content): string
    {
        $patterns = [
            '/{{\s*!\s*([a-zA-Z0-9_]+)\s*->\s*([a-zA-Z0-9_]+)\s*}}/', // Unescaped variable with context
            '/{{\s*!\s*([a-zA-Z0-9_]+)\s*}}/', // Unescaped variable
            '/{{\s*([a-zA-Z0-9_]+)\s*->\s*([a-zA-Z0-9_]+)\s*}}/', // Escaped variable with context
            '/{{\s*([a-zA-Z0-9_]+)\s*}}/', // Escaped variable
        ];

        $replcaements = [
            '<?php echo is_array(\$$1) ? \$$1["$2"] : \$$1->$2; ?>',
            '<?php echo \$$1; ?>',
            '<?php echo $__brain->escape(is_array(\$$1) ? \$$1["$2"] : \$$1->$2); ?>',
            '<?php echo $__brain->escape(\$$1); ?>',
        ];

        return preg_replace($patterns, $replcaements, $content);
    }

    protected function compileLoops(string $content): string
    {
        $patterns = [
            '/{{\s*#\s*([a-zA-Z0-9_]+)\s*}}/', // For
            '/{{\s*#\s*([a-zA-Z0-9_]+)\s+as\s+([a-zA-Z0-9_]+)\s*}}/', // Foreach
            '/{{\s*\/#\s*}}/', // Close statement
        ];

        $replcaements = [
            '<?php for ($index = 0; $index < \$$1; $index++) { ?>',
            '<?php foreach(\$$1 as $index => \$$2) { ?>',
            '<?php } ?>',
        ];

        return preg_replace($patterns, $replcaements, $content);
    }

    protected function compileConditions(string $content): string
    {
        $patterns = [
            '/{{\s*\?\s*([a-zA-Z0-9_]+)\s*}}/', // If
            '/{{\s*\/\?\s*}}/', // Close statement
        ];

        $replcaements = [
            '<?php if(isset(\$$1) && \$$1) { ?>',
            '<?php } ?>',
        ];

        return preg_replace($patterns, $replcaements, $content);
    }

    protected function compileInclusions(string $content): string
    {
        return preg_replace_callback('/{{\s*>\s*([a-zA-Z0-9_\-.]+)\s*([\S\s]*?)\s*}}/', function ($matches) {

            $variableMatches = $this->collectAssignements($matches[2]);

            $data = $this->compileAssignments($variableMatches);

            return '<?php echo $__brain->include(\'' . $matches[1] . '\', ' . $data . ') ?>';

        }, $content);
    }

    protected function compileSlots(string $content): string
    {
        $patterns = [
            '/{{\s*\$\s*([a-zA-Z0-9_]+)\s*}}/', // Start slot
            '/{{\s*\/\$\s*}}/', // Stop slot
        ];

        $replcaements = [
            '<?php $__brain->startSlot(\'$1\') ?>',
            '<?php $__brain->stopSlot() ?>',
        ];

        return preg_replace($patterns, $replcaements, $content);
    }

    protected function compileExtension(string $content): string
    {
        return preg_replace_callback('/{{\s*&\s*([a-zA-Z0-9_\-.]+)\s*([\S\s]*?)\s*}}(\r\n|\r|\n)?/', function ($matches) {

            $variableMatches = $this->collectAssignements($matches[2]);

            $this->extending = [
                'template' => $matches[1],
                'data' => $this->compileAssignments($variableMatches),
            ];

            return '';

        }, $content);
    }

    protected function collectAssignements(string $haystack): array
    {
        $matches = [];
        preg_match_all('/(:?[a-zA-Z0-9_]+?)="(.+?)"/', $haystack, $matches, PREG_SET_ORDER);
        return $matches;
    }

    protected function compileAssignments(array $matches): string
    {
        $data = '[';

        foreach ($matches as $match) {
            if (substr($match[1], 0, 1) === ':') {
                $data .= '\'' . substr_replace($match[1], '', 0, 1) . '\' => $' . $match[2] . ', ';
            } else {
                $data .= '\'' . $match[1] . '\' => \'' . addslashes($match[2]) . '\', ';
            }
        }

        $data .= ']';

        return $data;
    }

    protected function getExtension(): string
    {
        if (isset($this->extending)) {
            $extention = '<?php echo $__brain->include(\'' . $this->extending['template'] . '\', ' . $this->extending['data'] . ') ?>';
            $this->extending = null;
        }

        return $extention ?? '';
    }

    protected function getSource($filename): string
    {
        return "<?php /**SOURCE $filename **/ ?>";
    }
}
