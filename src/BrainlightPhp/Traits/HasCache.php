<?php

namespace Brainlight\BrainlightPhp\Traits;

use Brainlight\BrainlightPhp\Types\CacheId;

trait HasCache
{
    protected string $cacheDir;

    protected function cacheExists(CacheId $cacheId): bool
    {
        return file_exists($this->getCacheFilename($cacheId));
    }

    protected function saveCache(CacheId $cacheId, string $contents): void
    {
        file_put_contents($this->getCacheFilename($cacheId), $contents);
    }

    protected function getCacheContent(CacheId $cacheId): bool|string
    {
        $cacheFile = $this->getCacheFilename($cacheId);

        if (file_exists($cacheFile)) {
            return file_get_contents($cacheFile);
        }

        return false;
    }

    protected function getCacheFilename(CacheId $cacheId): string
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . $cacheId->getName() . '.php';
    }

    /**
     * Delete all files inside the cache directory with the same hash
     * as the one of the cache ID provided.
     *
     * @param CacheId $cacheId
     * @return void
     */
    protected function deleteRelatedCache(CacheId $cacheId): void
    {
        array_map('unlink', glob($this->cacheDir . DIRECTORY_SEPARATOR . $cacheId->hash . '_*.php'));
    }

}