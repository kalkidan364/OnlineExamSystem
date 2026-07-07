<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$q = App\Models\Question::find(56);
if ($q) {
    $q->options = [
        ['left' => 'KALKIDAN', 'right' => 'A,KALKIDAN'],
        ['left' => 'FITSUM', 'right' => 'B.FITSUM']
    ];
    $q->save();
    echo "Question 56 updated.";
} else {
    echo "Question 56 not found.";
}
