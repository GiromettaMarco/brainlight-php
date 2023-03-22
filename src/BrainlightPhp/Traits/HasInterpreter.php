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
     * Current depth level for slots.
     *
     * @var int
     */
    protected int $slotsLevel = 0;

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
        $this->slotsLevel++;
        $this->evaluate($filename, $data);
        $this->slotsLevel--;
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
        $this->slots[$slotName] = [
            'level' => $this->slotsLevel,
            'content' => ob_get_clean(),
        ];
    }

    /**
     * Recovers slots for the current level and remove them from the slot array.
     *
     * @return array
     */
    public function getSlots(): array
    {
        $collectedSlots = [];
        foreach ($this->slots as $slotName => $slot) {
            if ($slot['level'] === $this->slotsLevel) {
                $collectedSlots[$slotName] = $slot['content'];
                unset($this->slots[$slotName]);
            }
        }
        return $collectedSlots;
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

    public function contextualize(mixed $context, array $chain = [], bool $isset = false): mixed
    {
        if ($chain) {

            if (is_array($context)) {

                if ($isset && ! isset($context[$chain[0]])) {
                    return false;
                }

                return $this->contextualize($context[$chain[0]], array_splice($chain, 1), $isset);
            }

            if (is_object($context)) {

                if ($isset && ! isset($context->{$chain[0]})) {
                    return false;
                }

                return $this->contextualize($context->{$chain[0]}, array_splice($chain, 1), $isset);
            }

            // @todo type runtime exception here
            return false;
        }

        return $context;
    }
}
