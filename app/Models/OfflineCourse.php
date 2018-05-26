<?php

namespace App\Models;

use Exception;
use Request;
use Response;


class OfflineCourse extends BaseModule
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

    protected $table = 'offline_courses';

    protected $fillable = ['title', 'intro', 'content', 'feature', 'note', 'charging_type', 'price', 'bought_num', 'score', 'teacher_id', 'city_id', 'sort', 'state', 'published_at'];

    protected $dates = ['published_at'];

    protected $entities = [];

    public function comments()
    {
        return $this->hasMany(OfflineComment::class, 'course_id');
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'course_id');
    }

    public function carts()
    {
        return $this->morphMany(Cart::class, 'refer');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function scopeFilter($query, $filters)
    {
        $query->where(function ($query) use ($filters) {
            empty($filters['id']) ?: $query->where('id', $filters['id']);
            empty($filters['city_id']) ?: $query->where('city_id', $filters['city_id']);
            empty($filters['last_id']) ?: $query->where('id', $filters['order'], $filters['last_id']);
        });
    }

    public static function stores($input)
    {
        $input['state'] = static::STATE_NORMAL;

        $offlinecourse = static::create($input);

        //保存图片集
        if (isset($input['images'])) {
            Item::sync(Item::TYPE_IMAGE, $offlinecourse, $input['images']);

        }

        //保存音频集
        if (isset($input['audios'])) {
            Item::sync(Item::TYPE_AUDIO, $offlinecourse, $input['audios']);
        }

        //保存视频集
        if (isset($input['videos'])) {
            Item::sync(Item::TYPE_VIDEO, $offlinecourse, $input['videos']);
        }

        //保存标签
        if (isset($input['tags'])) {
            Tag::sync($offlinecourse, $input['tags']);
        }

        return $offlinecourse;
    }

    public static function updates($id, $input)
    {
        $offlinecourse = static::find($id);

        $offlinecourse->update($input);

        //保存图片集
        if (isset($input['images'])) {
            Item::sync(Item::TYPE_IMAGE, $offlinecourse, $input['images']);

        }

        //保存音频集
        if (isset($input['audios'])) {
            Item::sync(Item::TYPE_AUDIO, $offlinecourse, $input['audios']);
        }

        //保存视频集
        if (isset($input['videos'])) {
            Item::sync(Item::TYPE_VIDEO, $offlinecourse, $input['videos']);
        }

        //保存标签
        if (isset($input['tags'])) {
            Tag::sync($offlinecourse, $input['tags']);
        }

        return $offlinecourse;
    }

    public static function table()
    {
        $filters = Request::all();

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;

        $ds = new DataSource();
        $offlinecourses = static::with('user')
            ->filter($filters)
            ->orderBy('top', 'desc')
            ->orderBy('sort', 'desc')
            ->skip($offset)
            ->limit($limit)
            ->get();

        $ds->total = static::filter($filters)
            ->count();

        $offlinecourses->transform(function ($offlinecourse) {
            $attributes = $offlinecourse->getAttributes();

            //实体类型
            foreach ($offlinecourse->entities as $entity) {
                $entity_map = str_replace('_id', '_name', $entity);
                $entity = str_replace('_id', '', $entity);
                $attributes[$entity_map] = empty($offlinecourse->$entity) ? '' : $offlinecourse->$entity->name;
            }

            //日期类型
            foreach ($offlinecourse->dates as $date) {
                $attributes[$date] = empty($offlinecourse->$date) ? '' : $offlinecourse->$date->toDateTimeString();
            }
            $attributes['tags'] = implode(',', $offlinecourse->tags()->pluck('name')->toArray());
            $attributes['state_name'] = $offlinecourse->stateName();
            $attributes['created_at'] = empty($offlinecourse->created_at) ? '' : $offlinecourse->created_at->toDateTimeString();
            $attributes['updated_at'] = empty($offlinecourse->updated_at) ? '' : $offlinecourse->updated_at->toDateTimeString();
            return $attributes;
        });

        $ds->rows = $offlinecourses;

        return Response::json($ds);
    }

}