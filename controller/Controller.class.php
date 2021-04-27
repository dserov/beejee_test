<?php

class Controller
{
    public $view = 'index';
    public $title;
    public $ajax = false;

    function __construct()
    {
        $this->title = Config::get('sitename');
    }

    public function index() {
        return [];
    }
}