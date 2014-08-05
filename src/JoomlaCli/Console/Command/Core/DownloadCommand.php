<?php

namespace JoomlaCli\Console\Command\Core;

use JoomlaCli\Joomla\Versions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DownloadCommand
 *
 * @package JoomlaCli\Console\Command\Core
 */
class DownloadCommand extends Command
{
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
        $output->writeln('<info>Downloading release '. $release .'</info>');

        $tempFile = tempnam(sys_get_temp_dir(), 'Joomla-cli');

        $bytes = file_put_contents($tempFile, fopen($url, 'r'));
        if ($bytes === false || $bytes === 0) {
            throw new \RuntimeException(sprintf('Failed to download %s', $url));
        }

        // unpack
        `cd $target; tar xzf $tempFile --strip 1`;

        unlink($tempFile);
    }
}