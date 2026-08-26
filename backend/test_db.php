<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$instructors = App\Models\User::where('role', 'instructor')->with('department')->get();
echo "Total Instructors: " . $instructors->count() . "\n";
foreach ($instructors as $u) {
    echo "{$u->name} - Dept: " . ($u->department ? $u->department->name : 'None') . " - Section: {$u->section} - Year: {$u->year_level}\n";
}
