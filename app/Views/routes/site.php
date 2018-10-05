<?php
$app->get('/', 'HomeController:index');

$app->get('/cadastro', 'HomeController:cadastro');
$app->post('/cadastro', 'HomeController:cadastro');

$app->get('/login', 'HomeController:login');
$app->post('/login', 'HomeController:login');

$app->get('/logout', 'HomeController:logout');

$app->get('/recuperar-senha', 'HomeController:passwordRecovery');
$app->post('/recuperar-senha', 'HomeController:passwordRecovery');



$app->get('/home', 'HomeController:home');

$app->get('/lojas', 'HomeController:lojas');

$app->post('/create', 'HomeController:create');

$app->post('/', 'HomeController:HeaderUrls');

$app->get('/hello/{name}', 'HomeController:hello');

$app->get('/teste', 'HomeController:teste');

?>