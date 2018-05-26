<?php

namespace App\Models;

use Exception;
use Request;
use Response;


class OfflineComment extends BaseModule
{
    const STATE_DELETED = 0;
    const STATE_NORMAL = 1;
    const STATE_CANCELED = 2;
    const STATE_PUBLISHED = 9;

    const STATES = [
        0 => '已删除',
        1 => '未发布',
        2 => '已撤回',
        9 => '已发布',
    ];

    const STATE_PERMISSIONS = [
        0 => '@offlinecourse-delete',
        2 => '@offlinecourse-cancel',
        9 => '@offlinecourse-publish',
    ];

    protected $table = 'offline_comments';

    protected $fillable = ['record_id', 'course_id', 'content', 'score', 'member_id', 'teacher_id', 'like_num'];

    protected $dates = ['published_at'];

    public function course()
    {
        return $this->belongsTo(OfflineCourse::class);
    }

}