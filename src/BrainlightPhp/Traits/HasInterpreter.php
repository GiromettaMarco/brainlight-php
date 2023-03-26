<?php

namespace Brainlight\BrainlightPhp\Traits;

trait HasInterpreter
{
    use HasSlots;

    protected int $escapeFlags;

    protected ?string $escapeEncoding;

    protected bool $escapeDoubleEncode;

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
        $this->slots->increaseLevel();
        $this->evaluate($filename, $data);
        $this->slots->decreaseLevel();
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

                return $this->contextualize($context[$chain[0]], array_slice($chain, 1), $isset);
            }

            if (is_object($context)) {

                if ($isset && ! isset($context->{$chain[0]})) {
                    return false;
                }

                return $this->contextualize($context->{$chain[0]}, array_slice($chain, 1), $isset);
            }

            // @todo type runtime exception here
            return false;
        }

        return $context;
    }
}
