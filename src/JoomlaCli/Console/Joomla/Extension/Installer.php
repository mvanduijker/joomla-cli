<?php


namespace JoomlaCli\Console\Joomla\Extension;


use Symfony\Component\Finder\Finder;

class Installer
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
     * @var Installer\AdapterInterface
     */
    protected $adapter;

    /**
     * @var string
     */
    protected $installFile;

    public function __construct($path)
    {
        $this->path = $path;

        if (false === $this->findManifest()) {
            throw new \RuntimeException('No manifest file found in ' . $this->path);
        }

        $type = (string)$this->manifest['type'];

        if (!$type) {
            throw new \RuntimeException('Invalid manifest file ' . $this->path);
        }

        $this->adapter = $this->createAdapter($type);
    }

    public function getAdapter()
    {
        return $this->adapter;
    }


    protected function findManifest()
    {
        $finder = new Finder();
        $finder->depth('==0')->name('*.xml')->files();
        foreach ($finder->in($this->path) as $file) {
            /* @var $file \SplFileObject */

            $this->manifest = new \SimpleXMLElement($file->getContents());
            $this->installFile = $file->getBasename();
            return true;
        }

        return false;
    }

    protected function createAdapter($type)
    {
        $class = __NAMESPACE__ . '\Installer\Adapter\\' . ucfirst($type);

        if (!class_exists($class)) {
            throw new \RuntimeException('No adapter for extension type ' . $type);
        }

        var_dump($this->installFile);

        return new $class(pathinfo($this->path, PATHINFO_DIRNAME), $this->manifest, $this->installFile);
    }
}