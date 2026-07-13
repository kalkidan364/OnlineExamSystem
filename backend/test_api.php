<?php
$data = ['name'=>'Test', 'email'=>'test@wou.edu.et', 'role'=>'student', 'department_id'=>'', 'password'=>'12345678'];
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\nAccept: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true // to get the 422 body
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents('http://localhost:8000/api/v1/admin/users', false, $context);
echo "Result:\n" . $result . "\n";
