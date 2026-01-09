<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'phone',
        'name',
        'is_valid',
        'group_id',
        'metadata',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'metadata' => 'array',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
