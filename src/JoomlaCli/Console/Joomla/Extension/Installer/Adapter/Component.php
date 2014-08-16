<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

use JoomlaCli\Console\Joomla\Extension\Installer\AdapterInterface;
use Symfony\Component\Filesystem\Filesystem;

class Component implements AdapterInterface
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var \SimpleXMLElement
     */
    protected $manifest;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Constructor
     *
     * @param $path of the extension which needs to be installed
     * @param \SimpleXMLElement $manifest
     */
    public function __construct($path, \SimpleXMLElement $manifest)
    {
        $this->path = $path;

        $this->manifest = $manifest;

        $this->fs = new Filesystem();
    }

    /**
     * Main install procedure of copying the files
     *
     * @param $target root directory of Joomla installation
     * @throws \RuntimeException
     * @return void
     */
    public function install($target)
    {
        // filter name according to 'cmd' JFilterInput
        $name = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', (string) $this->manifest->name));

        if (!$name) {
            throw new \RuntimeException('Invalid manifest file, invalid name!');
        }

        // simulation from JInstallerAdapterComponent->install
        // the check is kind a bogus because underscores are always filtered out in $name
        if (substr($name, 0, 4) == 'com_') {
            $element = $name;
        } else {
            $element = 'com_' . $name;
        }

        if (!$this->manifest->administration) {
            throw new \RuntimeException('Invalid manifest file, administration tag missing');
        }

        $frontendTarget = rtrim($target, '/') . '/components/' . $element;
        $adminTarget = rtrim($target, '/') . '/administrator/components/' . $element;
        $frontendLanguageTarget = rtrim($target, '/') . '/language';
        $adminLanguageTarget = rtrim($target, '/') . '/administrator/language';

        // start copying files

        // copy frontend component files
        if ($this->manifest->files) {
            $this->installComponentFiles($this->manifest->files, $frontendTarget);
        }

        // copy administrator component files
        if ($this->manifest->administration->files) {
            $this->installComponentFiles($this->manifest->administration->files, $adminTarget);
        }

        // copy media files
        if ($this->manifest->media && $this->manifest->media['destination']) {
            $this->installMediaFiles($this->manifest->media, $target);
        }

        // copy frontend language files
        if ($this->manifest->languages) {
            $this->installLanguageFiles($this->manifest->languages, $frontendLanguageTarget);
        }

        // copy backend language files
        if ($this->manifest->administration->languages) {
            $this->installLanguageFiles($this->manifest->languages, $adminLanguageTarget);
        }

        // copy manifest script
        if ($this->manifest->scriptfile) {
            $this->installScriptFile($this->manifest->scriptfile, $adminTarget);
        }

        return true;
    }

    /**
     * Installation / copying of component files
     *
     * @param \SimpleXMLElement $element
     * @param $target base path + name of component directory, for example {joomla-installation-path}/components/com_example
     * @throws \RuntimeException
     */
    protected function installComponentFiles(\SimpleXMLElement $element, $target)
    {
        if ($element['folder']) {
            $source = $this->path . '/' . trim((string)$element['folder'], '/');

            if (!file_exists($source)) {
                throw new \RuntimeException('Source frontend component folder does not exists!');
            }

            $this->fs->mkdir($target);
            $this->fs->mirror($source, $target);
        } else {
            // copy per file in files element, when folder is set, it will copy all anyways
            // we silently ignore for now
        }
    }

    /**
     * Installation / copying of the media files
     * Target for media is always the same, so you must provide the base path of joomla installation
     *
     * @param \SimpleXMLElement $element media xml element
     * @param $target base path of joomla installation
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     */
    protected function installMediaFiles(\SimpleXMLElement $element, $target)
    {
        if ($element['folder']) {
            $mediaFolder = (string)$element['destination'];
            if (strpos($mediaFolder, '/../') !== false) {
                throw new \UnexpectedValueException('Media destination folder might be malicious!');
            }
            $mediaTarget =  rtrim($target, '/') . '/media/' . $mediaFolder;
            $mediaSource = $this->path . '/' . trim((string)$element['folder'], '/');

            if (!file_exists($mediaSource)) {
                throw new \RuntimeException('Source media folder does not exist!');
            }

            $this->fs->mkdir($mediaTarget);
            $this->fs->mirror($mediaSource, $mediaTarget);

        } else {
            // copy per file in files element, when folder is set, it will copy all anyways
            // we silently ignore for now
        }
    }

    /**
     * Install / copy script file
     *
     * @param \SimpleXMLElement $element
     * @param $target where the script file must be copied to (directory name)
     */
    protected function installScriptFile(\SimpleXMLElement $element, $target)
    {
        $scriptfile = (string)$element;
        if ($scriptfile && file_exists($this->path . '/' . $scriptfile)) {
            $scriptFileTarget = $target . '/' . $scriptfile;
            if (!file_exists(dirname($scriptFileTarget))) {
                $this->fs->mkdir(dirname($scriptFileTarget));
            }
            $this->fs->copy($this->path . '/' . $scriptfile, $scriptFileTarget);
        }
    }

    /**
     * Install / copy extension language files
     * This is sort of the Joomla 1.5 way, newer versions will magicly load from extension, you can still use this
     * for others to override the files more info http://docs.joomla.org/Manifest_files#Language_files
     *
     * @param \SimpleXMLElement $element
     * @param $target where the language files must te copied to example: {fullpath}/adminstrator/language
     */
    protected function installLanguageFiles(\SimpleXMLElement $element, $target)
    {
        $folderPrefix = '';
        if ($element['folder']) {
            $folderPrefix = (string)$element['folder'];
        }

        foreach($element->language as $lang) {
            if (!$lang['tag']) {
                continue;
            }
            $langFileSourcePath = $this->path . '/'. trim($folderPrefix,'/') . '/' . (string)$lang;
            $langFileTargetPath = $target . '/' . (string)$lang['tag'] . '/' .pathinfo($langFileSourcePath, PATHINFO_BASENAME);
            if (!file_exists($langFileSourcePath)) {
                continue;
            }

            $this->fs->copy($langFileSourcePath, $langFileTargetPath);
        }
    }
}