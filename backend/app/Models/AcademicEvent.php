<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'academic_year',
        'semester',
        'start_date',
        'end_date',
        'all_day',
        'start_time',
        'end_time',
        'status',
        'color',
        'is_recurring',
        'created_by',
    ];

    protected $casts = [
        'start_date'   => 'date:Y-m-d',
        'end_date'     => 'date:Y-m-d',
        'all_day'      => 'boolean',
        'is_recurring' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
