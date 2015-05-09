<?php

namespace JoomlaCli\Console\Model\Joomla;

/**
 * Class Database
 * @package JoomlaCli\Console\Model\Joomla
 */
class Database
{
    /**
     * @var string
     */
    protected $dbName;

    /**
     * @var string
     */
    protected $dbUser = 'root';

    /**
     * @var string
     */
    protected $dbPassword = '';

    /**
     * @var string
     */
    protected $dbHost = 'localhost';

    /**
     * @var string
     */
    protected $dbPrefix = 'jos_';

    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct(Array $options)
    {
        $this->setOptions($options);
    }

    public function createDatabase()
    {
        $dbh = $this->getDbh();
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $dbh->query('CREATE DATABASE ' . $this->getDbName() . ' CHARACTER SET utf8');
        $dbh->query('USE ' . $this->getDbName());

        return $this;
    }


    /**
     * @param array $options
     * @return $this
     * @throws \RuntimeException
     */
    public function setOptions(Array $options)
    {
        foreach ($options as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($this, $methodName) && is_callable([$this, $methodName])) {
                $this->{$methodName}($value);
            } else {
                throw new \RuntimeException('Illegal option ' . $key);
            }
        }

        return $this;
    }

    protected function check()
    {
        // check if everything is set
        foreach(['dbName', 'dbPassword', 'dbUser', 'dbHost', 'dbPrefix'] as $opt) {
            if ($this->{$opt} === null) {
                return false;
            }
        }

        return true;
    }

    protected function getDsn()
    {
        return 'mysql:dbname='.$this->getDbName() . ';charset=UTF8';
    }

    protected function getDbh()
    {
        if (null === $this->pdo) {
            $this->pdo = new \PDO($this->getDsn(), $this->getDbName(), $this->getDbPassword());
        }
    }

    /**
     * @param string $dbHost
     *
     * @return $this
     */
    public function setDbHost($dbHost)
    {
        $this->dbHost = $dbHost;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @param string $dbName
     *
     * @return $this
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @param string $dbPassword
     *
     * @return $this
     */
    public function setDbPassword($dbPassword)
    {
        $this->dbPassword = $dbPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbPassword()
    {
        return $this->dbPassword;
    }

    /**
     * @param string $dbPrefix
     *
     * @return $this
     */
    public function setDbPrefix($dbPrefix)
    {
        $this->dbPrefix = $dbPrefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbPrefix()
    {
        return $this->dbPrefix;
    }

    /**
     * @param string $dbUser
     *
     * @return $this
     */
    public function setDbUser($dbUser)
    {
        $this->dbUser = $dbUser;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbUser()
    {
        return $this->dbUser;

        return $this;
    }
}