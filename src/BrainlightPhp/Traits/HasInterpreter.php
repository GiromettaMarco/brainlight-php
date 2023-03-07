<?php

namespace Brainlight\BrainlightPhp\Traits;

trait HasInterpreter
{
    protected int $escapeFlags;

    protected ?string $escapeEncoding;

    protected bool $escapeDoubleEncode;

    /**
     * Stack of slot names provided at runtime.
     * 
     * @var array
     */
    protected array $slotNames = [];

    /**
     * Associative array with slots names and their values.
     *
     * @var array
     */
    protected array $slots = [];

    /**
     * Evaluates a cached template and returns its output.
     *
     * @param string $filename Absolute path to the compiled template
     * @param array $data Variables passed to the template
     * @return mixed The output produced by the template
     */
    protected function evaluateTemplate(string $filename, array $data = []): string
    {
        ob_start();
        $this->evaluate($filename, $data);
        return ob_get_clean();
    }

    /**
     * Evaluates a cached template in a confined context.
     *
     * @param string $filename
     * @param array $data
     * @return mixed
     */
    protected function evaluate(string $filename, array $data = []): mixed
    {
        $__path = $filename;
        $__data = $data;
        $__brain = $this;

        return (static function () use ($__path, $__data, $__brain) {
            extract($__data, EXTR_SKIP);
            return require $__path;
        })();
    }

    /**
     * Starts a slot directive.
     *
     * @param string $slotName
     */
    public function startSlot(string $slotName)
    {
        $this->slotNames[] = $slotName;
        ob_start();
    }

    /**
     * Stops a slot directive.
     */
    public function stopSlot()
    {
        $slotName = array_pop($this->slotNames);
        $this->slots[$slotName] = ob_get_clean();
    }

    /**
     * Escapes a string.
     *
     * @param string $string
     * @return string
     */
    public function escape(string $string): string
    {
        return htmlspecialchars($string, $this->escapeFlags, $this->escapeEncoding, $this->escapeDoubleEncode);
    }
}
