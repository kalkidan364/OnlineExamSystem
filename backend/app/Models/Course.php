<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'credits',
        'semester',
        'level',
        'section',
        'department_id',
        'instructor_id',
        'status',
        'created_by',
        'start_date',
        'end_date',
        'co_instructor_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function coInstructor()
    {
        return $this->belongsTo(User::class, 'co_instructor_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'course_code', 'code');
    }
}
