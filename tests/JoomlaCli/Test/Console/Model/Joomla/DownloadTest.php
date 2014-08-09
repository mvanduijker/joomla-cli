<?php

namespace JoomlaCli\Test\Console\Model\Joomla;

use JoomlaCli\Console\Model\Joomla\Download;

class DownloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Download
     */
    protected $model;

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * @var string
     */
    protected $downloadPath = 'https://github.com/joomla/joomla-cms/archive/refs/tags/3.3.3.tar.gz';


    public function testDownloadCached()
    {
        $download = new Download($this->cachePath);
        $download->download('https://github.com/joomla/joomla-cms/archive/refs/tags/3.3.3.tar.gz', '3.3.3', $this->target);

        $this->assertEquals(true, file_exists($this->cachePath . '/3.3.3'));
        $this->assertEquals(true, file_exists($this->target . '/includes'));
        $this->assertEquals(true, is_dir($this->target . '/includes'));

    }

    public function testDownloadUncached()
    {
        $download = new Download($this->cachePath);
        $download->download('https://github.com/joomla/joomla-cms/archive/refs/tags/3.3.3.tar.gz', '3.3.3', $this->target, false);

        $this->assertEquals(false, file_exists($this->cachePath . '/3.3.3'));
        $this->assertEquals(true, file_exists($this->target . '/includes'));
        $this->assertEquals(true, is_dir($this->target . '/includes'));
    }

    protected function setUp()
    {
        $this->cachePath = sys_get_temp_dir() . '/joomla-cli-download-test-cache';
        $this->target = sys_get_temp_dir() . '/joomla-cli-download-test-target';
    }

    protected function tearDown()
    {
        // cleanup the cache folder
        $path = escapeshellarg($this->cachePath);
        `rm -rf $path`;

        // cleanup target folder
        $path = escapeshellarg($this->target);
        `rm -rf $path`;
    }


}