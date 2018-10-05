<?php
$app->get('/admin2018Lendary', 'HomeControllerAdmin:adm');

$app->get('/admin2018Lendary/lojas', 'HomeControllerAdmin:lojas');
$app->post('/admin2018Lendary/lojas', 'HomeControllerAdmin:lojas');

$app->get('/admin2018Lendary/produtos', 'HomeControllerAdmin:produtos');

?>