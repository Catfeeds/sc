<?php

namespace App\Models;

use Exception;
use Request;
use Response;


class Record extends BaseModule
{
    const STATE_NOPAY = 0;
    const STATE_PAID = 1;
    const STATE_ACCEPTED = 2;
    const STATE_CONFIRMED = 3;
    const STATE_FINISHED = 4;
    const STATE_COMPLAIN = 5;
    const STATE_COMPLAINING = 6;
    const STATE_COMPLAINED = 7;
    const STATE_REFUNDING = 8;
    const STATE_CANCLED = 9;

    const STATES = [
        0 => '未支付',
        1 => '已支付，未接受',
        2 => '已接受，未上课',
        3 => '已上课，待评价',
        4 => '约课完成',
        5 => '投诉待处理',
        6 => '投诉处理中',
        7 => '投诉处理结束',
        8 => '待退款',
        9 => '已取消',
    ];

    const STATE_PERMISSIONS = [
        0 => '@offlinecourse-delete',
        2 => '@offlinecourse-cancel',
        9 => '@offlinecourse-publish',
    ];

    protected $table = 'offline_records';

    protected $fillable = ['course_id', 'charging_type ', 'price', 'num', 'total_price', 'diff_price', 'start_at', 'finish_at', 'member_id', 'teacher_id', 'state'];

    protected $dates = ['published_at'];

    public function comments()
    {
        return $this->hasMany(OfflineComment::class);
    }

    public function course()
    {
        return $this->belongsTo(OfflineCourse::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}