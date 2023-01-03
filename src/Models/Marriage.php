<?php

namespace Yumi\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Marriage extends Model
{
    public $timestamps = false;
    protected $table = 'marriages';
    protected $guarded = [];

    protected $casts
        = [
            'married_since' => 'datetime:d.m.Y H:i:s',
            'divorced_since' => 'datetime:d.m.Y H:i:s',
        ];

    public function husband(): BelongsTo
    {
        return $this->belongsTo(User::class, 'husband_id');
    }

    public function wife(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wife_id');
    }

    public function daysSinceMarriage(): Attribute
    {
        return Attribute::get(
            fn() => $this->married_since->diffForHumans(Carbon::now(), [
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                'options' => CarbonInterface::SEQUENTIAL_PARTS_ONLY
                    | CarbonInterface::JUST_NOW
            ])
        );
    }
}