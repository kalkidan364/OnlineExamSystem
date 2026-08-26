<?php
$ch = curl_init('http://localhost:8000/api/v1/admin/users-export?role=instructor&format=pdf');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
// Include CORS Origin header
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: http://localhost:5173'
]);
$response = curl_exec($ch);
echo $response;
