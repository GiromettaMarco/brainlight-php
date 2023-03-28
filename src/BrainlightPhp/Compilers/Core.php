<?php

namespace Brainlight\BrainlightPhp\Compilers;

use Brainlight\BrainlightPhp\Types\Extension;

class Core
{
    protected ?string $filename;
    protected array $tokens = [];
    protected bool $tagOpen = false;
    protected ?Extension $extending;
    public string $compiled;

    public function __construct(string $string, ?string $filename = null)
    {
        $this->filename = $filename;

        $this->tokens = preg_split('/({{)\s*([\S\s]*?)\s*(}})/', $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $this->compileTokens();

        $this->appendExtension();
        $this->appendSource();
    }

    protected function compileTokens(): void
    {
        $this->compiled = '';

        foreach ($this->tokens as $token) {
            $this->compileToken($token);
        }
    }

    protected function compileToken(string $token): void
    {
        if ($this->tagOpen) {
            if ($token === '}}') {
                $this->tagOpen = false;
            } else {
                $this->compileTag($token);
            }
        } else {
            if ($token === '{{') {
                $this->tagOpen = true;
            } else {
                $this->compileRaw($token);
            }
        }
    }

    protected function compileRaw(string $string): void
    {
        $this->compiled .= $string;
    }

    protected function compileTag($tag): void
    {
        preg_match('/^(!|\?|\/\?|#|\/#|>|\$|\/\$|&|-)*\s*([\S\s]*)$/', $tag, $matches);

        switch ($matches[1]) {
            case '':
                $this->compiled .= Tags\Escaped::compile($matches[2]);
                break;

            case '!':
                $this->compiled .= Tags\Unescaped::compile($matches[2]);
                break;

            case '?':
                $this->compiled .= Tags\Condition::open($matches[2]);
                break;

            case '/?':
                $this->compiled .= Tags\Condition::close();
                break;

            case '#':
                $this->compiled .= Tags\Loop::open($matches[2]);
                break;

            case '/#':
                $this->compiled .= Tags\Loop::close();
                break;

            case '>':
                $this->compiled .= Tags\Inclusion::compile($matches[2]);
                break;

            case '&':
                $this->extending = Tags\Inclusion::getExtension($matches[2]);
                break;

            case '$':
                $this->compiled .= Tags\Slot::open($matches[2]);
                break;

            case '/$':
                $this->compiled .= Tags\Slot::close();
                break;

            default:
                break;
        }
    }

    protected function appendExtension(): void
    {
        if (isset($this->extending)) {
            $this->compiled .= $this->extending->compile();
        }
    }

    protected function appendSource(): void
    {
        if (isset($this->filename)) {
            $this->compiled .= "<?php /**SOURCE $this->filename **/ ?>";
        }
    }

    public static function compile(string $string, ?string $filename = null): string
    {
        return (new Core($string, $filename))->compiled;
    }
}