<?php

namespace JoomlaCli\Console\Command\Extension\Language;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListCommand
 *
 * Lists available languages that can be installed
 *
 * @package JoomlaCli\Console\Command\Extension\Language
 */
class ListCommand extends Command
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('extension:archive:install')
            ->setDescription('Install a joomla extension from an archive (like zip file). This is a pure file based installer, doesn\'r require a database connection')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Extension to install (archive), can also be an url'
            )
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to valid working joomla installation',
                getcwd()
            );
    }

    /**
     * Execute the program
     *
     * @param InputInterface  $input  cli input object
     * @param OutputInterface $output cli output object
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->check($input);

        $this->installArchive($input, $output);

    }

    /**
     * Check if program can be run
     *
     * @param InputInterface $input cli input object
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function check(InputInterface $input)
    {
        // check valid joomla installation
        $path = $input->getOption('path');
        if (!file_exists($path)) {
            throw new \RuntimeException('Path does not exist: ' . $path);
        }

        if (!is_dir($path)) {
            throw new \RuntimeException('Path is not a directory: '. $path);
        }

        if (!file_exists(rtrim($path, '/') . '/configuration.php')) {
            throw new \RuntimeException('configuration.php not found, probably no joomla installation in: ' . $path);
        }
    }

    /**
     * Install the extension
     *
     * @param InputInterface  $input  input cli object
     * @param OutputInterface $output output cli object
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function installArchive(InputInterface $input, OutputInterface $output)
    {

        $file = $input->getArgument('file');
        $isRemote = false;

        // check if a url, if a url we are going to save to a tmp file
        if (preg_match('/^http(s){0,1}\:\/\//i', $file)) {
            $isRemote = true;
            //
        }

        //if (file_exists($input->getArgument('file')))
    }
}