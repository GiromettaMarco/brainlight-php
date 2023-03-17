<?php

namespace Brainlight\BrainlightPhp\Traits;

trait HasCompiler
{
    protected ?array $extending;

    protected function compile(string $filename): string
    {
        $content = $this->compileSlots(file_get_contents($filename));
        $content = $this->compileExtension($content);
        $content = $this->compileAdvancedExtension($content);
        $content = $this->compileInclusions($content);
        $content = $this->compileAdvancedInclusions($content);
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
            '/{{\s*!\s*([a-zA-Z0-9_]+)\s*>\s*([a-zA-Z0-9_]+)\s*}}/', // Unescaped variable with context
            '/{{\s*!\s*([a-zA-Z0-9_]+)\s*}}/', // Unescaped variable
            '/{{\s*([a-zA-Z0-9_]+)\s*>\s*([a-zA-Z0-9_]+)\s*}}/', // Escaped variable with context
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
            '/{{\s*#\s*([a-zA-Z0-9_]+)\s+as\s+([a-zA-Z0-9_]+)\s*}}/', // Foreach (with value)
            '/{{\s*#\s*([a-zA-Z0-9_]+)\s+as\s+([a-zA-Z0-9_]+)\s*=>\s*([a-zA-Z0-9_]+)\s*}}/', // Foreach (with key => value)
            '/{{\s*\/#\s*}}/', // Close statement
        ];

        $replcaements = [
            '<?php for ($index = 0; $index < \$$1; $index++) { ?>',
            '<?php foreach(\$$1 as \$$2) { ?>',
            '<?php foreach(\$$1 as \$$2 => \$$3) { ?>',
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

            $data = $this->compileAssignments($matches[2]);

            return '<?php echo $__brain->include(\'' . $matches[1] . '\', ' . $data . ') ?>';

        }, $content);
    }

    protected function compileAdvancedInclusions(string $content): string
    {
        return preg_replace_callback('/{{\s*>\+\s*([a-zA-Z0-9_\-.]+)\s*([\S\s]*?)\s*}}/', function ($matches) {

            $data = $this->compileAssignments($matches[2]);

            return '<?php echo $__brain->includeWithLogic(\'' . $matches[1] . '\', ' . $data . ') ?>';

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

            $this->extending = [
                'template' => $matches[1],
                'data' => $this->compileAssignments($matches[2]),
                'advanced' => false,
            ];

            return '';

        }, $content);
    }

    protected function compileAdvancedExtension(string $content): string
    {
        return preg_replace_callback('/{{\s*&\+\s*([a-zA-Z0-9_\-.]+)\s*([\S\s]*?)\s*}}(\r\n|\r|\n)?/', function ($matches) {

            $this->extending = [
                'template' => $matches[1],
                'data' => $this->compileAssignments($matches[2]),
                'advanced' => true,
            ];

            return '';

        }, $content);
    }

    protected function compileAssignments(string $haystack): string
    {
        $matches = [];
        preg_match_all('/\S+/', $haystack, $matches);

        $data = '[';

        foreach ($matches[0] as $assignement) {

            $assMatches = [];

            if (preg_match('/(:?[a-zA-Z0-9_]+?)="(.+?)"/', $assignement, $assMatches)) {
                // Expanded syntax

                if (substr($assMatches[1], 0, 1) === ':') {
                    // Assign variable
                    $data .= '\'' . substr_replace($assMatches[1], '', 0, 1) . '\' => $' . $assMatches[2] . ', ';
                } else {
                    // Assign string
                    $data .= '\'' . $assMatches[1] . '\' => \'' . addslashes($assMatches[2]) . '\', ';
                }

            } elseif (substr($assignement, 0, 1) === ':') {
                // Short-hand syntax
                $varName = substr_replace($assignement, '', 0, 1);
                $data .= '\'' . $varName . '\' => $' . $varName . ', ';
            }
        }

        $data .= ']';

        return $data;
    }

    protected function getExtension(): string
    {
        if (isset($this->extending)) {

            if ($this->extending['advanced']) {
                $extention = '<?php echo $__brain->includeWithLogic(\'' . $this->extending['template'] . '\', ' . $this->extending['data'] . ') ?>';
            } else {
                $extention = '<?php echo $__brain->include(\'' . $this->extending['template'] . '\', ' . $this->extending['data'] . ') ?>';
            }

            $this->extending = null;
        }

        return $extention ?? '';
    }

    protected function getSource($filename): string
    {
        return "<?php /**SOURCE $filename **/ ?>";
    }
}
