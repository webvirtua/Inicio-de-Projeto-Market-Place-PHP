<?php
//$app->get('/admLoja', 'HomeControllerAdmin:admin');
$app->get('/admLoja', 'HomeControllerAdmLoja:adm');

$app->get('/admLoja/atributos', 'HomeControllerAdmLoja:atributos');
$app->post('/admLoja/atributos', 'HomeControllerAdmLoja:atributos');

$app->get('/admLoja/produtos', 'HomeControllerAdmLoja:produtos');
$app->post('/admLoja/produtos', 'HomeControllerAdmLoja:produtos');

$app->get('/admLoja/produtos/{produto}', 'HomeControllerAdmLoja:produto'); //acesso dinâmico {produto}
$app->post('/admLoja/produtos/{produto}', 'HomeControllerAdmLoja:produto');

?>