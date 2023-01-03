<?php

namespace Yumi\Models;

use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    public $timestamps = false;
    protected $table = 'statistics';
    protected $guarded = [];
    protected $casts
        = [
            'date' => 'date:d.m.Y'
        ];
}