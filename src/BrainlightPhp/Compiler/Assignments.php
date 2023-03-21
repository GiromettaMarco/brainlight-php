<?php

namespace Brainlight\BrainlightPhp\Compiler;

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
            $this->compileShorthand($token, $last) ||
            $this->compileVariable($token, $last) ||
            $this->compileString($token, $last) ||
            $this->compileBoolAndInt($token, $last) ||
            $this->compileContext($token, $last)
        ) {
            return true;
        }
        return false;
    }

    protected function compileShorthand(string $token, bool $last = false): bool
    {
        if (preg_match('/^:([a-zA-Z0-9_]+)$/', $token, $matches)) {

            $this->compiled .= "'$matches[1]' => \$$matches[1]";

            if ( ! $last) {
                $this->compiled .= ', ';
            }

            return true;
        }

        return false;
    }

    protected function compileVariable(string $token, bool $last = false): bool
    {
        if (preg_match('/^:([a-zA-Z0-9_]+)=([a-zA-Z0-9_]+)$/', $token, $matches)) {

            $this->compiled .= "'$matches[1]' => \$$matches[2]";

            if ( ! $last) {
                $this->compiled .= ', ';
            }

            return true;
        }

        return false;
    }

    protected function compileString(string $token, bool $last = false): bool
    {
        if (preg_match('/^([a-zA-Z0-9_]+)="([^"]*)"$/', $token, $matches)) {

            $this->compiled .= "'$matches[1]' => '" . addslashes($matches[2]) . "'";

            if ( ! $last) {
                $this->compiled .= ', ';
            }

            return true;
        }

        return false;
    }

    protected function compileBoolAndInt(string $token, bool $last = false): bool
    {
        if (preg_match('/^([a-zA-Z0-9_]+)=([0-9]*|true|false)$/', $token, $matches)) {

            $this->compiled .= "'$matches[1]' => $matches[2]";

            if ( ! $last) {
                $this->compiled .= ', ';
            }

            return true;
        }

        return false;
    }

    protected function compileContext(string $token, bool $last = false): bool
    {
        if (preg_match('/^:([a-zA-Z0-9_]+)=([a-zA-Z0-9_]+)>([a-zA-Z0-9_]+)$/', $token, $matches)) {

            $this->compiled .= "'$matches[1]' => is_array(\$$matches[2]) ? \$$matches[2]['$matches[3]'] : \$$matches[2]->$matches[3]";

            if ( ! $last) {
                $this->compiled .= ', ';
            }

            return true;
        }

        return false;
    }

    public static function compile(string $string): string
    {
        return (new Assignments($string))->compiled;
    }
}