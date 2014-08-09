<?php

namespace JoomlaCli\Console\Model;

/**
 * Class Config
 *
 * @package JoomlaCli\Console\Model
 */
class Config extends \Zend\Config\Config
{

    /**
     * Constructor
     *
     * @param bool $allowModifications readonly flag
     */
    public function __construct($allowModifications = false)
    {
        $home = self::getHomeDir();
        $cache = self::getCacheDir($home);

        parent::__construct(
            [
                'home' => $home,
                'cache-dir' => $cache
            ],
            $allowModifications
        );
    }

    /**
     * Get home-dir based on env
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getHomeDir()
    {
        $homeDir = getenv('JOOMLA_CLI_HOME');
        if (!$homeDir) {
            if (!getenv('HOME')) {
                throw new \RuntimeException('The HOME or JOOMLA_CLI_HOME environment variable must be set for joomla-cli to work correctly');
            }
            $homeDir = rtrim(getenv('HOME')) . '/.joomla-cli';
        }

        return $homeDir;
    }

    /**
     * Get the cache-dir
     *
     * @param string $homeDir path to home-dir
     *
     * @return string
     */
    protected static function getCacheDir($homeDir)
    {
        $cacheDir = getenv('JOOMLA_CLI_CACHE_DIR');
        if (!$cacheDir) {
            $cacheDir = $homeDir . '/cache';
        }

        return $cacheDir;
    }
}