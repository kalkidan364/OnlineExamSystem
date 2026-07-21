<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        // Return key-value pairs
        $settings = SystemSetting::pluck('value', 'key');
        return response()->json($settings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'academicYear' => 'required|string',
            'semester' => 'required|string',
        ]);

        SystemSetting::updateOrCreate(['key' => 'academicYear'], ['value' => $request->academicYear]);
        SystemSetting::updateOrCreate(['key' => 'semester'], ['value' => $request->semester]);

        return response()->json(['message' => 'Settings updated successfully']);
    }
}
