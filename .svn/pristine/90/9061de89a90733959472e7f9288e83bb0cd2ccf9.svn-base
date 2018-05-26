<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiveMember extends Model
{
    const TYPE_CLOSE = 0;
    const TYPE_NORMAL = 1;

    protected $fillable = [
        'live_id',
        'member_id',
        'room_id',
        'state'
    ];

    const STATES = [
        0 => '已删除',
        1 => '正常',
    ];

    protected $table = 'live_member';

    protected $dates = ['deleted_at'];

}