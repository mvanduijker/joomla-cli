<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

use Joomla\Archive\Archive;
use JoomlaCli\Console\Joomla\Extension\Installer;

/**
 * Class Package
 *
 * @package JoomlaCli\Console\Joomla\Extension\Installer\Adapter
 * @see http://docs.joomla.org/Package
 */
class Package extends Base
{
    /**
     * @var string null
     */
    protected $tmpDir = null;

    /**
     * Main install procedure of copying the files
     *
     * @param $target root directory of Joomla installation
     * @throws \RuntimeException
     * @return void
     */
    public function install($target)
    {

        $name = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', (string) $this->manifest->packagename));

        if (!$name) {
            throw new \RuntimeException('Invalid manifest file, invalid library name!');
        }

        // Joomla Archiver needs JPATH_ROOT to be set, so let's set it here. (a bit stupid if you ask me...)
        if (!defined('JPATH_ROOT')) {
            define('JPATH_ROOT', $target);
        }

        // install files
        if ($this->manifest->files) {
            $this->installPackageFiles($this->manifest->files, $target);

        }

        // install manifest
        $this->installManifest($target);

        return true;
    }

    /**
     * Installation / copying of Library files
     *
     * @param \SimpleXMLElement $element
     * @param $target base path + name of library, for example {joomla-installation-path}/libraries/example
     * @throws \RuntimeException
     */
    protected function installPackageFiles(\SimpleXMLElement $element, $target)
    {
        if ($element['folder']) {
            $source = $this->path . '/' . trim((string)$element['folder'], '/');
        } else {
            $source = $this->path;
        }

        if (count($element->children())) {
            foreach($element->children() as $child) {
                $file = $source . '/' . (string)$child;

                if (is_file($file) && file_exists($file)) {
                    $this->installPackageFile($file, $target);
                }
            }
        }
    }

    /**
     * Install a single package (archived file)
     *
     * @param $source
     * @return $this
     */
    protected function installPackageFile($source, $target)
    {
        if (!file_exists($source)) {
            throw new \RuntimeException('Source library folder does not exists!');
        }

        // first unpack to tmp dir, then install the directory
        $tmpFolder = $this->getTmpDir() . '/' . pathinfo($source, PATHINFO_FILENAME);
        $this->fs->mkdir($tmpFolder);

        $archive = new Archive();
        $archive->extract($source, $tmpFolder);

        // install the extension
        $installer = new Installer($tmpFolder);
        $installer->getAdapter()->install($target);

        // cleanup tmp folder
        $this->fs->remove($tmpFolder);

        return $this;

    }

    /**
     * Get the tmp dir to extract the packages to, if no tmpDir is set it will default to
     * sys_get_temp_dir()
     *
     * @return string
     */
    public function getTmpDir()
    {
        if (null === $this->tmpDir) {
            $this->tmpDir = sys_get_temp_dir();
        }

        return $this->tmpDir;
    }

    /**
     * Set the tmp dir to extract the packages to
     *
     * @param $tmpDir
     * @return $this
     */
    public function setTmpDir($tmpDir)
    {
        if (file_exists($tmpDir) && is_dir($tmpDir) && is_writable($tmpDir)) {
            $this->tmpDir = $tmpDir;
        } else {
            throw new \InvalidArgumentException('Given tmpdir does not exist, is not writable or is not a directory.');
        }

        return $this;

    }

    /**
     * Install manifest to manifests folder (probably for uninstaller)
     *
     * @param $target string
     */
    protected function installManifest($target)
    {
        $target .= '/administrator/manifests/packages/' . $this->installFile;
        $this->fs->copy($this->path . '/' . $this->installFile, $target);
    }
}