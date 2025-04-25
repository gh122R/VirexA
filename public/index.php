<?php

/**
 * Здесь просто объявляю заголовки для CORS, включаю autoloader и web.php, содержащий маршруты.
 */

declare(strict_types=1);

use App\Router;

require_once dirname(__DIR__) . '/vendor/autoload.php';
include dirname(__DIR__) . '/src/app/web.php';

header("Cross-Origin-Opener-Policy: same-origin-allow-popups");
header("Cross-Origin-Embedder-Policy: credentialless");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

echo Router::handler($_SERVER['REQUEST_URI']);