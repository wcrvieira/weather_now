<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Controller\WeatherController;

// Em produção, remova estas linhas ou redirecione para logs
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Configura o timezone padrão
date_default_timezone_set('America/Sao_Paulo');

$controller = new WeatherController();
$controller->render();