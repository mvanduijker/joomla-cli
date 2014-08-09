<?php

namespace JoomlaCli\Test\Console\Model;


use JoomlaCli\Console\Model\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testHomeDir()
    {
        // test with HOME env
        putenv('HOME=/tmp');
        $config = new Config();
        $this->assertEquals('/tmp/.joomla-cli', $config->get('home'));
        $this->assertEquals('/tmp/.joomla-cli/cache', $config->get('cache-dir'));

        // test with JOOMLA_CLI_HOME env
        putenv('JOOMLA_CLI_HOME=/tmp/joomla-cli-test');
        $config = new Config();
        $this->assertEquals('/tmp/joomla-cli-test', $config->get('home'));
        $this->assertEquals('/tmp/joomla-cli-test/cache', $config->get('cache-dir'));
    }

    public function testCacheDir()
    {
        putenv('JOOMLA_CLI_CACHE_DIR=/tmp');
        $config = new Config();
        $this->assertEquals('/tmp', $config->get('cache-dir'));
    }
}