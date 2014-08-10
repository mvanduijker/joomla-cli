<?php

use JoomlaCli\Console\Model\Config;
use JoomlaCli\Console\Command\Core\DownloadCommand;
use JoomlaCli\Console\Command\Core\InstallDbCommand;
use JoomlaCli\Console\Command\Core\UpdateDbCommand;
use JoomlaCli\Console\Command\Extension\Language\InstallCommand;
use JoomlaCli\Console\Command\Extension\Language\ListCommand;
use JoomlaCli\Console\Model\Joomla\Download;
use JoomlaCli\Console\Model\Joomla\Versions;
use Pimple\Container;
use Symfony\Component\Console\Application;

$c = new Container();

$c['model.config'] = function () {
    return new Config();
};

$c['model.joomla.download'] = function ($c) {
    return new Download($c['model.config']->get('cache-dir') . '/releases');
};

$c['model.joomla.versions'] = function ($c) {
    return new Versions($c['model.config']->get('cache-dir') . '/versions.json');
};

$c['command.core.download'] = function ($c) {
    return new DownloadCommand($c['model.joomla.download'], $c['model.joomla.versions']);
};

$c['command.core.installdb'] = function ($c) {
    return new InstallDbCommand();
};

$c['command.core.updatedb'] = function ($c) {
    return new UpdateDbCommand();
};

$c['command.extension.language.install'] = function ($c) {
    return new InstallCommand();
};

$c['command.extension.language.list'] = function ($c) {
    return new ListCommand();
};

$c['app'] = function ($c) {
    $app = new Application('Joomla-cli', '1.0');
    $app->addCommands(
        [
            $c['command.core.download'],
            $c['command.core.installdb'],
            $c['command.core.updatedb'],
            $c['command.extension.language.install'],
            $c['command.extension.language.list'],
        ]
    );

    return $app;
};

return $c;