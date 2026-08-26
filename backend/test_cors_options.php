<?php
$ch = curl_init('http://localhost:8000/api/v1/admin/users-export?role=instructor&format=pdf');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: http://localhost:5173',
    'Access-Control-Request-Method: GET',
    'Access-Control-Request-Headers: authorization, accept'
]);
$response = curl_exec($ch);
echo $response;
