<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

use JoomlaCli\Console\Joomla\Extension\Installer\AdapterInterface;
use Symfony\Component\Filesystem\Filesystem;

abstract class Base implements AdapterInterface
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
     * @var string
     */
    protected $installFile;

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
    public function __construct($path, \SimpleXMLElement $manifest, $installFile)
    {
        $this->path = $path;

        $this->manifest = $manifest;

        $this->installFile = $installFile;

        $this->fs = new Filesystem();
    }

    /**
     * Main install procedure of copying the files
     *
     * @param $target root directory of Joomla installation
     * @throws \RuntimeException
     * @return void
     */
    abstract public function install($target);

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