<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'established',
        'head_id',
        'status',
    ];

    /** The user assigned as department head */
    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    /** Users belonging to this department */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
