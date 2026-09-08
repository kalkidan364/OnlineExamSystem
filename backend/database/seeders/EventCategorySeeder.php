<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventCategory;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Academic Event',
                'description' => 'General academic activities such as classes, orientation, etc.',
                'color'       => '#22C55E',
                'type'        => 'system',
                'status'      => 'active',
            ],
            [
                'name'        => 'Examination',
                'description' => 'All examination related events and periods.',
                'color'       => '#3B82F6',
                'type'        => 'system',
                'status'      => 'active',
            ],
            [
                'name'        => 'Holiday',
                'description' => 'University holidays and public holidays.',
                'color'       => '#EF4444',
                'type'        => 'system',
                'status'      => 'active',
            ],
        ];

        foreach ($categories as $cat) {
            EventCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
