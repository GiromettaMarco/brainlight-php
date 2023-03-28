<?php

namespace Brainlight\BrainlightPhp\Compilers;

class Variable
{
    public string $compiled = '';
    public bool $escape;

    public function __construct(string $statement, bool $escape = true)
    {
        $this->escape = $escape;

        $this->run($statement);
    }

    protected function run($statement): void
    {
        $context = Context::compile($statement);

        if ($context !== '') {
            $this->start();
            $this->compiled .= $context;
            $this->stop();
        }
    }

    protected function start(): void
    {
        $this->compiled .= '<?php echo $__brain->print(';
    }

    protected function stop(): void
    {
        if ( ! $this->escape) {
            $this->compiled .= ', false';
        }

        $this->compiled .= '); ?>';
    }

    public static function compile(string $statement, bool $escape = true): string
    {
        return (new Variable($statement, $escape))->compiled;
    }

    public static function print(string $statement, bool $escape = true): string
    {
        return static::compile($statement, $escape);
    }
}