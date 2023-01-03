<?php

namespace Yumi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Statistics extends Model
{
    public $timestamps = false;
    protected $table = 'statistics';
    protected $guarded = [];
    protected $casts
        = [
            'date' => 'date:d.m.Y'
        ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}