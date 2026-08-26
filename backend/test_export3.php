<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/api/v1/admin/users-export', 'GET', ['role'=>'instructor', 'format'=>'pdf']);
$controller = app()->make(\App\Http\Controllers\Api\V1\AdminUserController::class);
try {
    $controller->export($request);
    echo "Success\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . " - " . $e->getMessage() . "\n";
}
