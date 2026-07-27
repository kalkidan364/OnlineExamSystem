<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$q = App\Models\Question::where('type', 'matching')->whereNotNull('question_data')->first();
echo gettype($q->question_data) . "\n";
echo json_encode($q->toArray());
