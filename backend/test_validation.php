<?php
require 'vendor/autoload.php';
\ = require_once 'bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();

\ = [
    'type' => 'multiple_choice',
    'title' => 'Instruction',
    'instruction' => '',
    'description' => '',
    'text' => '<p>chooose</p>',
    'correct_answer' => '',
    'explanation' => '',
    'marks' => 1,
    'difficulty' => 'Medium',
    'chapter' => 'Chapter 1',
    'settings' => [
        'requiredQuestion' => true,
        'allowPartialMarks' => false,
        'negativeMarking' => false,
        'showExplanation' => true
    ],
    'question_data' => [
        'options' => [
            ['label' => 'A', 'text' => ''],
            ['label' => 'B', 'text' => ''],
            ['label' => 'C', 'text' => ''],
            ['label' => 'D', 'text' => '']
        ],
        'shuffle_choices' => true
    ],
    'status' => 'draft'
];
\ = validator(\, [
    'type'           => 'required|string',
    'title'          => 'nullable|string|max:255',
    'description'    => 'nullable|string',
    'instruction'    => 'nullable|string',
    'difficulty'     => 'nullable|string',
    'chapter'        => 'nullable|string',
    'topic'          => 'nullable|string',
    'text'           => 'required|string',
    'options'        => 'nullable|array',
    'correct_answer' => 'nullable|string',
    'explanation'    => 'nullable|string',
    'image_url'      => 'nullable|string',
    'marks'          => 'required|integer|min:1',
    'negative_marks' => 'nullable|integer',
    'time_seconds'   => 'nullable|integer',
    'status'         => 'nullable|string',
    'tags'           => 'nullable|string',
    'settings'       => 'nullable|array',
    'question_data'  => 'nullable|array',
]);
if (\->fails()) {
    dump(\->errors()->toArray());
} else {
    echo 'Validation Passed';
}

