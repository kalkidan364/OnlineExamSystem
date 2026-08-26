<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the export with roles=instructor,dept_head
$roles = explode(',', 'instructor,dept_head');
$users = App\Models\User::whereIn('role', $roles)->with('department')->get();
echo "Total exported: " . $users->count() . "\n";
foreach ($users as $u) {
    echo "{$u->name} | role:{$u->role} | dept:" . ($u->department ? $u->department->name : 'None') . "\n";
}
