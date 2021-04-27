<?php

trait Singleton
{
    static $_instance;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
