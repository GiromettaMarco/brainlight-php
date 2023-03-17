<?php

namespace Brainlight\BrainlightPhp;

use Brainlight\BrainlightPhp\CacheId;

class Engine
{
    use Traits\HasCache,
    Traits\HasCompiler,
    Traits\HasInterpreter,
    Traits\HasLogic;

    protected mixed $templatesDir;

    protected mixed $partialsDir;

    protected string $extension;

    public function __construct(array $options)
    {
        if (! isset($options['cacheDir'])) {
            throw new \BadMethodCallException('A path for cache storage must be provided.');
        }

        // Set options
        $this->cacheDir = $options['cacheDir'];
        $this->templatesDir = $options['templatesDir'] ?? false;
        $this->partialsDir = $options['partialsDir'] ?? null;
        $this->logicNamespace = $options['logicNamespace'] ?? false;
        $this->extension = $options['extension'] ?? 'brain';
        $this->escapeFlags = $options['escapeFlags'] ?? ENT_QUOTES;
        $this->escapeEncoding = $options['escapeEncoding'] ?? 'UTF-8';
        $this->escapeDoubleEncode = $options['escapeDoubleEncode'] ?? true;
    }

    /**
     * Renders a template by name.
     *
     * @param string $template The template name
     * @param array $variables Key paired array with values for substitutions
     * @return string The parsed template as a string
     */
    public function render(string $template, array $variables = []): string
    {
        $templatePath = $this->resolveTemplateName($template);

        return $this->renderTemplate($templatePath, $variables);
    }

    /**
     * Renders a partial template by name.
     *
     * @param string $template
     * @param array $variables
     * @return string
     */
    public function include(string $template, array $variables = []): string
    {
        $templatePath = $this->resolveTemplateName($template, true);

        return $this->renderTemplate($templatePath, array_merge($variables, $this->slots));
    }

    /**
     * Renders a partial template with additional logic.
     *
     * @param string $template
     * @param array $variables
     * @return string
     */
    public function includeWithLogic(string $template, array $variables = []): string
    {
        $logic = new ($this->resolveLogicNamespace($template))($template);

        return $this->include($logic->template, $logic->filterVariables($variables));
    }

    /**
     * Renders a template by its fully qualified name.
     *
     * @param string $templatePath
     * @param array $variables
     * @return string
     */
    protected function renderTemplate(string $templatePath, array $variables = []): string
    {
        // Give the file a cache ID
        $templateCacheId = new CacheId($templatePath);

        // If failed to recover cache
        if (!$this->cacheExists($templateCacheId)) {

            // Delete previous cache
            $this->deleteRelatedCache($templateCacheId);

            // Compile the template and save it as cache
            $this->saveCache($templateCacheId, $this->compile($templatePath));

        }

        // Render from cache
        return $this->evaluateTemplate($this->getCacheFilename($templateCacheId), $variables);
    }

    /**
     * Converts a template name in its absolute filename.
     *
     * @param string $template The template name
     * @param bool $partial Whether the template is a partial or not
     * @return string The template filename
     */
    protected function resolveTemplateName(string $template, bool $partial = false): string
    {
        if ($partial && isset($this->partialsDir)) {
            return $this->resolveTemplatePath($template, $this->partialsDir);
        } else {
            return $this->resolveTemplatePath($template, $this->templatesDir);
        }
    }

    /**
     * Searches for a template by its name and returns its absolute filename if found.
     *
     * If $templatesPath is an array, it searches between al path specified in it.
     * If $templatesPath is false, the template name is considered to be an absolute path.
     * Throws an InvalidArgumentException if the template can't be found.
     *
     * @param string $template
     * @param mixed $templatesPath
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function resolveTemplatePath(string $template, mixed $templatesPath): string
    {
        if ($templatesPath) {

            // Relative paths
            if (is_array($templatesPath)) {

                // Search in more folders
                foreach ($templatesPath as $templatesPath) {
                    $path = realpath($this->resolveDots($template, $templatesPath));
                    if ($path) {
                        break;
                    }
                }

            } else {

                // Search in one folder
                $path = realpath($this->resolveDots($template, $templatesPath));

            }

        } else {

            $path = realpath($template);

        }

        if ($path) { return $path; }
        throw new \InvalidArgumentException("View [$template] not found.");
    }

    /**
     * Converts dots in the template name to directory separators and appends the Brainlight file extension.
     *
     * @param string $name
     * @param string|null $prepend
     * @return string
     */
    protected function resolveDots(string $name, ?string $prepend = null): string
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $name) . '.' . $this->extension;
        if ($prepend) {
            $path = $prepend . DIRECTORY_SEPARATOR . $path;
        }
        return $path;
    }
}
