<?php

namespace JoomlaCli\Console;

use Zend\Config\Config;

class Factory
{
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

    protected static function getCacheDir($homeDir)
    {
        $cacheDir = getenv('JOOMLA_CLI_CACHE_DIR');
        if (!$cacheDir) {
            $cacheDir = $homeDir . '/cache';
        }

        return $cacheDir;
    }

    public static function createConfig()
    {
        $home = self::getHomeDir();
        $cache = self::getCacheDir($home);

        $config = new Config(
            [
                'home' => $home,
                'cache-dir' => $cache
            ]
        );

        // we can start loading config files here :)

        return $config;
    }
}


