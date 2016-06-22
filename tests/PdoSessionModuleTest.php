<?php

namespace Ray\SymfonySessionModule;

use Ray\Di\Injector;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class PdoSessionModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testModule()
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        (new PdoSessionHandler($pdo))->createTable();

        $lifetime = 60 * 60 * 24; // 1 day

        $module = new PdoSessionModule($pdo, ['cookie_lifetime' => $lifetime]);
        $injector = new Injector($module, $_ENV['TMP_DIR']);
        $instance = $injector->getInstance(SessionInterface::class);

        $this->assertInstanceOf(SessionInterface::class, $instance);

        /* @var $instance SessionInterface */
        $instance->start();

        $this->assertEquals($lifetime, $instance->getMetadataBag()->getLifetime());
        $this->assertEquals($lifetime, ini_get('session.cookie_lifetime'));
    }
}
