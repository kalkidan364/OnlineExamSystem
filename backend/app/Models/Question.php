<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id',
        'exam_id',
        'type',
        'instruction',
        'text',
        'options',
        'correct_answer',
        'marks',
        'difficulty',
        'chapter',
        'topic',
        'negative_marks',
        'time_seconds',
        'status',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    /**
     * The question bank this question belongs to.
     */
    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    /**
     * The exam this question is assigned to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
