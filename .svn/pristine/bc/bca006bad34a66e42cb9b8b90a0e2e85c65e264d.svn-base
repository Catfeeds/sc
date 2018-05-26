<?php

namespace App\Models;

use Auth;
use Gate;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends BaseModule
{
    use SoftDeletes;

    const STATE_DELETED = 0;
    const STATE_NORMAL = 1;
    const STATE_PASSED = 9;

    const DEFAULT_NUM = 10;

    const STATES = [
        0 => '已删除',
        1 => '未审核',
        9 => '已审核',
    ];

    const PARENT_ROOT = 0;

    const NO_STARS = 0;

    const TYPE_ARTICLE = 1;
    const TYPE_QUESTION = 2;

    const STATE_PERMISSIONS = [
        0 => '@comment-delete',
        9 => '@comment-pass',
    ];

    protected $fillable = [
        'refer_id',
        'refer_type',
        'parent_id',
        'content',
        'like_num',
        'ip',
        'member_id',
        'to_member_id',
        'stars',
        'state',
    ];

    public function refer()
    {
        return $this->morphTo();
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function toMember()
    {
        return $this->belongsTo(Member::class, 'to_member_id');
    }

    public function scopeFilter($query, $filters)
    {
        $filters['order'] = empty($filters['order']) ? '<' : '>';

        $query->where(function ($query) use ($filters) {
            !empty($filters['refer_id']) ? $query->where('refer_id', $filters['refer_id']) : '';
            empty($filters['last_id']) ?: $query->where('id', $filters['order'], $filters['last_id']);
            !empty($filters['refer_type']) ? $query->where('refer_type', urldecode($filters['refer_type'])) : '';
        });
        if (isset($filters['state'])) {
            if (!empty($filters['state'])) {
                $query->where('state', $filters['state']);
            } else if ($filters['state'] === strval(static::STATE_DELETED)) {
                $query->onlyTrashed();
            }
        }
    }
}