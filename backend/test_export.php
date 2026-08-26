<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/v1/admin/users-export', 'GET', ['role'=>'instructor', 'format'=>'pdf']);
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() === 500) {
    echo $response->getContent();
}
