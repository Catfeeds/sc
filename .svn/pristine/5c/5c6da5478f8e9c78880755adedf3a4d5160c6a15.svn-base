<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
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

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeFilter($query, $filters)
    {
        $filters['order'] = empty($filters['order']) ? '<' : '>';

        $query->where(function ($query) use ($filters) {
            empty($filters['id']) ?: $query->where('id', $filters['id']);
            empty($filters['last_id']) ?: $query->where('id', $filters['order'], $filters['last_id']);
        });
    }
}