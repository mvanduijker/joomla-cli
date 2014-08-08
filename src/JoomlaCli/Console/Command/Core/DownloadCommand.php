<?php

namespace JoomlaCli\Console\Command\Core;

use JoomlaCli\Joomla\Versions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Config\Config;

/**
 * Class DownloadCommand
 *
 * @package JoomlaCli\Console\Command\Core
 */
class DownloadCommand extends Command
{
    /**
     * @var string
     */
    protected $version;

    /**
     * @var Versions
     */
    protected $versions;

    /**
     * @var Array
     */
    protected $release;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var bool
     */
    protected $keepInstallationFolder;

    /**
     * @var
     */
    protected $globalConfig;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        parent::__construct();
        $this->globalConfig = $config;
    }

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('core:download')
            ->setDescription('Download joomla core')
            ->addOption(
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'Target path for Joomla download',
                'joomla'
            )
            ->addOption(
                'joomla-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'Joomla version',
                '3.*'
            )
            ->addOption(
                'keep-installation-folder',
                null,
                InputOption::VALUE_NONE,
                'Keep the installation folder after download'
            );
    }

    /**
     * Implement execute method
     *
     * @param InputInterface  $input  input object to retrieve input from commandline
     * @param OutputInterface $output output object to perform output actions
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->target = $input->getOption('path');
        $this->version = $input->getOption('joomla-version');
        $this->versions = new Versions();
        $this->release = $this->versions->getVersion($this->version);
        $this->keepInstallationFolder = $input->getOption('keep-installation-folder');

        $this->check();
        $this->doDownload($output);

    }

    /**
     * Perform some checks before we start executing real stuff
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function check()
    {
        if (file_exists($this->target)) {
            throw new \RuntimeException('Directory ' . $this->target . ' already exists!');
        }

        if (!$this->release) {
            throw new \RuntimeException('Could not find version of ' . $this->version);
        }
    }

    /**
     * Perform the download and extraction to target directory
     *
     * @param OutputInterface $output output object to perform output actions
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function doDownload(OutputInterface $output)
    {
        $target = escapeshellarg($this->target);

        $returnValue = `mkdir -p $target`;
        if ($returnValue) {
            throw new \RuntimeException('Could not create directory ' . $this->target);
        }

        $release = array_keys($this->release)[0];
        $url = array_values($this->release)[0];


        if (!file_exists($this->globalConfig->get('cache-dir') . '/releases')) {
            $output->writeln('<info>Cache dir does not exist, creating...</info>');
            $cacheDir = escapeshellarg($this->globalConfig->get('cache-dir') . '/releases');
            `mkdir -p $cacheDir`;
            unset($cacheDir);
        }

        $cachePath = $this->globalConfig->get('cache-dir') . '/releases/' . $release;

        if (file_exists($cachePath)) {
            // load from cache
            $output->writeln('<info>Loading from cache!</info>');
        } else {
            // download to cache
            $output->writeln('<info>Downloading release '. $release .'</info>');
            $bytes = file_put_contents($cachePath, fopen($url, 'r'));
            if ($bytes === false || $bytes === 0) {
                throw new \RuntimeException(sprintf('Failed to download %s', $url));
            }
        }

        $cachePathEscaped = escapeshellarg($cachePath);

        // unpack
        `cd $target; tar xzf $cachePathEscaped --strip 1`;

        if (!$this->keepInstallationFolder) {
            $installationFolder = escapeshellarg($this->target . '/installation');

            `rm -rf $installationFolder`;
        }

        if (!$this->versions->isTag($release)) {
            unlink($cachePath);
        }


    }
}