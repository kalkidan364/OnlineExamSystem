<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$exam = App\Models\Exam::with('questions')->latest()->first();
foreach($exam->questions as $q){
    echo $q->id . ": " . $q->text . " => " . $q->correct_answer . "\n";
}
