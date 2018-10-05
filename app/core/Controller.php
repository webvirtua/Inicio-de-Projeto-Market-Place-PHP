<?php
namespace App\core;

class Controller
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($property)
    {
        if ($this->container->{$property}) {
            return$this->container->{$property};
        }
    }
    
    public static function twig()
    {
        $loader =  new \Twig_Loader_Filesystem(__DIR__ .'/../Views/site');
        $twig = new \Twig_Environment($loader);

        return $twig;
    }

    public static function email()
    {
        $loader =  new \Twig_Loader_Filesystem(__DIR__ .'/../Views/emails');
        $twig = new \Twig_Environment($loader);

        return $twig;
    }
}
