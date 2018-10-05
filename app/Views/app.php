<?php
session_start();

require __DIR__ .'/../../vendor/autoload.php';

$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true //false em producao
    //video aula colocou conexão com banco aqui https://www.youtube.com/watch?v=70IkLMkPyPs&list=PLfdtiltiRHWGc_yY90XRdq6mRww042aEC&index=7
]];
$app = new \Slim\App($config);

$container = $app->getContainer();
//Container view
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ .'/../Views/site', [
        'cache' => false, //__DIR__ .'/../../views-cache',
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

//Container admin
/*$container['admin'] = function ($container) {
    $admin = new \Slim\Views\Twig(__DIR__ .'/../Views/site', [
        'cache' => __DIR__ .'/../../views-cache',
    ]);

    $admin->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $admin;
};*/

$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController($container);
};

$container['HomeControllerAdmin'] = function ($container) {
    return new \App\Controllers\HomeControllerAdmin($container);
};

$container['HomeControllerAdmLoja'] = function ($container) {
    return new \App\Controllers\HomeControllerAdmLoja($container);
};

require __DIR__ .'/routes/site.php';
require __DIR__ .'/routes/admin.php';
require __DIR__ .'/routes/adm-lojas.php';
?>