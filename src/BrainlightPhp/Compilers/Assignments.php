<?php

namespace Brainlight\BrainlightPhp\Compilers;

class Assignments
{
    protected array $tokens = [];
    public string $compiled;

    public function __construct(string $string)
    {
        preg_match_all('/\S*"[^"]*"|[^"\s]+/', $string, $this->tokens);
        $this->tokens = $this->tokens[0];
        $this->compileTokens();
    }

    protected function compileTokens(): void
    {
        $this->compiled = '[';

        foreach ($this->tokens as $key => $token) {
            $this->compileToken($token, ($key === array_key_last($this->tokens)));
        }

        $this->compiled .= ']';
    }

    protected function compileToken(string $token, bool $last = false): bool
    {
        if (
            Assignments\Shorthand::compile($token, $last, $this->compiled) ||
            Assignments\Variable::compile($token, $last, $this->compiled) ||
            Assignments\Text::compile($token, $last, $this->compiled) ||
            Assignments\Constant::compile($token, $last, $this->compiled)
        ) {
            return true;
        }
        return false;
    }

    public static function compile(string $string): string
    {
        return (new Assignments($string))->compiled;
    }
}