<?php

namespace JoomlaCli\Console\Command\Extension\Language;

use JoomlaCli\Console\Joomla\Bootstrapper;
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
            ->setName('extension:language:list')
            ->setDescription('Install a joomla language')
            ->addArgument(
                'list',
                InputArgument::OPTIONAL,
                'What to list: available || installed',
                'available'
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

        if ($input->getArgument('list') === 'installed') {
            $this->listInstalledLanguages($input, $output);
        } else {
            $this->listAvailableLanguages($input, $output);
        }

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
     * List available languages
     *
     * @param InputInterface  $input  input cli object
     * @param OutputInterface $output output cli object
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function listAvailableLanguages(InputInterface $input, OutputInterface $output)
    {
        $joomlaApp = Bootstrapper::getApplication($input->getOption('path'));
        $joomlaApp->set('list_limit', 10000);

        \JModelLegacy::addIncludePath($joomlaApp->getPath() . '/administrator/components/com_installer/models', 'InstallerModel');
        /* @var $model \InstallerModelLanguages */
        $model = \JModelLegacy::getInstance('Languages', 'InstallerModel');
        $model->findLanguages();
        $items = $model->getItems();

        foreach ($items as $item) {
            $output->writeln($item->name);
        }

    }

    /**
     * List installed languages
     *
     * @param InputInterface  $input  input cli object
     * @param OutputInterface $output output cli object
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function listInstalledLanguages(InputInterface $input, OutputInterface $output)
    {
        $joomlaApp = Bootstrapper::getApplication($input->getOption('path'));
        $joomlaApp->set('list_limit', 10000);

        \JModelLegacy::addIncludePath($joomlaApp->getPath() . '/administrator/components/com_languages/models', 'LanguagesModel');
        /* @var $model \InstallerModelLanguages */
        $model = \JModelLegacy::getInstance('Installed', 'LanguagesModel');
        $items = $model->getData();

        foreach ($items as $item) {
            $output->writeln($item->name);
        }
    }
}