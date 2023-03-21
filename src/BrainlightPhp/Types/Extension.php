<?php

namespace Brainlight\BrainlightPhp\Types;

class Extension
{
    public string $template;
    public string $data;
    public bool $advanced;

    public function __construct(string $template, ?string $data = null, bool $advanced = false)
    {
        $this->template = $template;
        $this->data = $data ?? '[]';
        $this->advanced = $advanced;
    }

    public function compile(): string
    {
        $code = '<?php echo $__brain->';

        if ($this->advanced) {
            $code .= 'includeExtensionWithLogic';
        } else {
            $code .= 'includeExtension';
        }

        return $code .= '(\'' . $this->template . '\', ' . $this->data . ') ?>';
    }
}
