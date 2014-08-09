<?php

namespace JoomlaCli\Console\Model\Joomla;

class Download
{
    protected $cachePath;

    public function __construct($cachePath)
    {
        $this->cachePath = $cachePath;
        $this->check();
    }

    protected function check()
    {
        if (!file_exists($this->cachePath)) {
            // create the cache path
            $path = escapeshellarg($this->cachePath);
            `mkdir -p $path`;
        }

        if (!file_exists($this->cachePath) || !is_dir($this->cachePath) || !is_writeable($this->cachePath)) {
            throw new \RuntimeException('Cache path not accessible! ' . $this->cachePath);
        }
    }

    protected function checkTarget($target)
    {
        if (!file_exists($target)) {
            $targetEscaped = escapeshellarg($target);

            $returnValue = `mkdir -p $targetEscaped`;
            if ($returnValue) {
                throw new \RuntimeException('Could not create directory ' . $target);
            }
        }
    }

    public function download($url, $release, $target, $cacheDownload = true)
    {
        $this->checkTarget($target);

        $cachedRelease = $this->cachePath . '/' . $release;

        if (!file_exists($cachedRelease)) {
            $bytes = file_put_contents($cachedRelease, fopen($url, 'r'));
            if ($bytes === false || $bytes === 0) {
                throw new \RuntimeException(sprintf('Failed to download %s', $url));
            }
        }

        // unpack
        $cachePathEscaped = escapeshellarg($cachedRelease);
        $targetEscaped    = escapeshellarg($target);
        `cd $targetEscaped; tar xzf $cachePathEscaped --strip 1`;

        if (!$cacheDownload) {
            unlink($cachedRelease);
        }
    }
}