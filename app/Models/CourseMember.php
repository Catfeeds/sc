<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMember extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'member_id',
    ];

    protected $table = 'course_member';

}