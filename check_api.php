<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make("Illuminate\Contracts\Http\Kernel");
$request = Illuminate\Http\Request::create("/admin/rab/total-dibayar-klien/1", "GET");
$response = $kernel->handle($request);
echo $response->getContent();
