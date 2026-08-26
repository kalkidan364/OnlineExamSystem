<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/v1/admin/users-export', 'GET', ['role'=>'instructor', 'format'=>'pdf']);
try {
    $response = $kernel->handle($request);
    if ($response->getStatusCode() === 500 && $response->exception) {
        echo get_class($response->exception) . ": " . $response->exception->getMessage() . "\n";
    }
} catch (\Throwable $e) {
    echo get_class($e) . ": " . $e->getMessage() . "\n";
}
