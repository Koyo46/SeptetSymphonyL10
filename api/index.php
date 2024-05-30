<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

// アプリケーションをブートストラップする
$app->boot();

// APIルートのみをロードする
$app->router->group(['prefix' => 'api'], function ($router) {
    require __DIR__.'/../routes/api.php';
});

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);