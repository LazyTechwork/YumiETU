<?php

namespace Yumi\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = true;
    protected $table = 'users';
    protected $guarded = [];

    public function name(): Attribute
    {
        return Attribute::get(
            fn() => $this->custom_name ??
                implode(
                    ' ',
                    array_filter([$this->first_name, $this->last_name],
                        static fn($it) => $it !== null)
                )
        );
    }
}