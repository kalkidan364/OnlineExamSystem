<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$qs = App\Models\Question::where('type', 'matching')->get();
foreach($qs as $q) {
    echo $q->id . ' - ' . $q->text . "\n";
    echo "DATA: " . json_encode($q->question_data) . "\n\n";
}
