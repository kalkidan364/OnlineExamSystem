<?php
$token = '197|QhAjrpfPpOtuFt27HDtZ937LYwHCwnx4oxSOdV5n6d0dd023';
$ch = curl_init('http://localhost:8000/api/v1/admin/users-export?role=instructor&format=pdf');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: http://localhost:5173',
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
$response = curl_exec($ch);
echo substr($response, 0, 500);
