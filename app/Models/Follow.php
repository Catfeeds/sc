<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $fillable = [
        'refer_id',
        'refer_type',
        'member_id',
    ];

    public function refer()
    {
        return $this->morphTo();
    }

}