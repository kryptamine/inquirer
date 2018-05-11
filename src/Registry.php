<?php

namespace Inquirer;

use Silex;
use Monolog;

class Registry
{
    private static $instance;

    /** @var Silex\Application */
    private $app;

    private function __construct()
    {
    }

    public function setApp(Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return Silex\Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return Monolog\Logger
     */
    public function getLog()
    {
        return $this->getApp()['monolog'];
    }

    /**
     * @return Registry
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
