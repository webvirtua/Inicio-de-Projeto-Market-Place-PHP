<?php
session_cache_expire(480);

require __DIR__ . '/app/Views/app.php';

$app->run();

ini_set('display_errors', 1);

error_reporting(E_ALL);
?>
