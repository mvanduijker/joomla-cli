<?php

namespace JoomlaCli\Console\Command\Extension\Language;

use JoomlaCli\Console\Joomla\Bootstrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 *
 * Installs a language in Joomla
 *
 * @package JoomlaCli\Console\Command\Extension\Language
 */
class InstallCommand extends Command
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('extension:language:install')
            ->setDescription('Install a joomla language')
            ->addArgument(
                'language',
                InputArgument::REQUIRED,
                'Language to install'
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
        $this->installLanguage($input, $output);

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
     * Install language
     *
     * @param InputInterface  $input  input cli object
     * @param OutputInterface $output output cli object
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function installLanguage(InputInterface $input, OutputInterface $output)
    {
        $joomlaApp = Bootstrapper::getApplication($input->getOption('path'));

        $lang = $input->getArgument('language');


        \JModelLegacy::addIncludePath($joomlaApp->getPath() . '/administrator/components/com_installer/models', 'InstallerModel');
        /* @var $model \InstallerModelLanguages */
        $model = \JModelLegacy::getInstance('Languages', 'InstallerModel');
        $model->findLanguages();
        $model->setState('list.limit', 10000);
        $items = $model->getItems();

        foreach ($items as $item) {
            if (strtoupper($item->name) === strtoupper($lang)) {

                $output->writeln('<info>Installing language '. $lang .'</info>');
                $model->install([$item->update_id]);
                return;
            }
        }

        throw new \RuntimeException('Language ' . $lang . ' not found!');

    }
}