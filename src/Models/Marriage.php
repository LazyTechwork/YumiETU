<?php

namespace Yumi\Models;

use Illuminate\Database\Eloquent\Model;

class Marriage extends Model
{
    public $timestamps = false;
    protected $table = 'marriages';
    protected $guarded = [];
}