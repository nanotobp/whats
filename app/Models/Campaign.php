<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'content',
        'image_path',
        'image_url',
        'group_id',
        'send_to_all',
        'status',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'read_count',
        'failed_count',
        'scheduled_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'send_to_all' => 'boolean',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getDeliveryRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return min(100, round(($this->delivered_count / $this->sent_count) * 100, 2));
    }

    public function getReadRateAttribute(): float
    {
        if ($this->delivered_count === 0) return 0;
        return min(100, round(($this->read_count / $this->delivered_count) * 100, 2));
    }
}
