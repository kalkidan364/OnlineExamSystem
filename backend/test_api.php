<?php
use Illuminate\Support\Facades\Http;
$user = \App\Models\User::find(1);
$token = $user->createToken('test')->plainTextToken;
echo json_encode(['token' => $token]);

