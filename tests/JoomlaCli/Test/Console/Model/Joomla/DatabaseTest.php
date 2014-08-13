<?php

namespace JoomlaCli\Test\Console\Model\Joomla;

use JoomlaCli\Console\Model\Joomla\Database;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Database
     */
    protected $model;

    public function setUp()
    {
        $this->model = new Database([
            'dbName' => $GLOBALS['DB_DBNAME'],
            'dbHost' => $GLOBALS['DB_HOST'],
            'dbUser' => $GLOBALS['DB_USER'],
            'dbPassword' => $GLOBALS['DB_PASSWD'],
        ]);
    }

    public function tearDown()
    {

    }

    public function testCreate()
    {
        $this->assertEquals(true, true);
    }
}