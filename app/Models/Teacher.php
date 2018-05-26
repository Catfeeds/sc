<?php

namespace App\Models;

class Teacher extends BaseModule
{
    const ID_ADMIN = 1;

    const REFER_TYPE = 'App\Models\Teacher';

    protected $fillable = [
        'member_id',
        'title',
        'cover_url',
        'city_id',
        'teach_exp',
        'organization',
        'background',
        'self_outcome',
        'teach_outcome',
        'meet_num',
        'recommended',
    ];

    protected $table = 'offline_teachers';

    public function scopeFilter($query, $filters)
    {
        $query->where(function ($query) use ($filters) {
            empty($filters['id']) ?: $query->where('id', $filters['id']);
            empty($filters['city_id']) ?: $query->where('city_id', $filters['city_id']);
            empty($filters['last_id']) ?: $query->where('id', '<', $filters['last_id']);
        });
    }

    public function courses()
    {
        return $this->hasMany(OfflineCourse::class, 'teacher_id');
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function comments()
    {
        return $this->hasMany(OfflineComment::class);
    }
}
