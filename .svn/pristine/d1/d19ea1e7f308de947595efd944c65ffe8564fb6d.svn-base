<?php

namespace App\Models;

use Exception;
use Request;
use Response;

class Course extends BaseModule
{
    const STATE_DELETED = 0;
    const STATE_NORMAL = 1;
    const STATE_CANCELED = 2;
    const STATE_PUBLISHED = 9;

    const SORT_TIME = 0;
    const SORT_HOT = 1;
    const SORT_EVALUATE = 2;
    const SORT_GENERAL = 3;

    const NO_RECOMMENDED = 0;
    const IS_RECOMMENDED = 1;
    const IS_FREE = 1;

    const MODEL_CLASS = 'App\Models\Course';
    const HOT_TEXT = 'hot_text';

    const STATES = [
        0 => '已删除',
        1 => '未发布',
        2 => '已撤回',
        9 => '已发布',
    ];

    const STATE_PERMISSIONS = [
        0 => '@course-delete',
        2 => '@course-cancel',
        9 => '@course-publish',
    ];

    protected $fillable = ['id', 'sort', 'parent_id', 'category_id', 'member_id', 'type', 'title', 'subtitle', 'intro', 'cover_url', 'poster_url', 'price', 'income', 'lesson_num', 'view_num', 'bought_num', 'recommended', 'recommended_seq', 'state', 'user_id', 'created_at', 'updated_at', 'is_free'];

    protected $entities = ['user_id'];

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function members()
    {
        return $this->belongsToMany(Member::class);
    }

    public function carts()
    {
        return $this->morphMany(Cart::class, 'refer');
    }

    public function courseViews()
    {
        return $this->hasMany(CourseView::class);
    }

    public static function stores($input)
    {
        $input['state'] = static::STATE_PUBLISHED;

        $course = static::create($input);

        return $course;
    }

    public static function updates($id, $input)
    {
        $course = static::find($id);

        if ($input['is_free'] == Course::IS_FREE) {
            $input['price'] = 0;
        }

        $course->update($input);

        return $course;
    }

    public static function table()
    {
        $filters = Request::all();

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;

        $ds = new DataSource();
        $courses = static::with('user')
            ->filter($filters)
            ->orderBy('recommended', 'desc')
            ->orderBy('sort', 'desc')
            ->skip($offset)
            ->limit($limit)
            ->get();

        $ds->total = static::filter($filters)
            ->count();

        $courses->transform(function ($course) {
            $course->price = $course->price / 100;
            $attributes = $course->getAttributes();

            //实体类型
            foreach ($course->entities as $entity) {
                $entity_map = str_replace('_id', '_name', $entity);
                $entity = str_replace('_id', '', $entity);
                $attributes[$entity_map] = empty($course->$entity) ? '' : $course->$entity->name;
            }

            //日期类型
            foreach ($course->dates as $date) {
                $attributes[$date] = empty($course->$date) ? '' : $course->$date->toDateTimeString();
            }

            $attributes['state_name'] = $course->stateName();
            $attributes['created_at'] = empty($course->created_at) ? '' : $course->created_at->toDateTimeString();
            $attributes['updated_at'] = empty($course->updated_at) ? '' : $course->updated_at->toDateTimeString();
            return $attributes;
        });

        $ds->rows = $courses;

        return Response::json($ds);
    }

    /**
     * 排序
     */
    public static function sort()
    {
        $select_id = request('select_id');
        $place_id = request('place_id');
        $move_down = request('move_down');

        $select = self::find($select_id);
        $place = self::find($place_id);

        if (empty($select) || empty($place)) {
            return Response::json([
                'status_code' => 404,
                'message' => 'ID不存在',
            ]);
        }

        if ($select->top && !$place->top) {
            return Response::json([
                'status_code' => 404,
                'message' => '置顶记录不允许移至普通位置',
            ]);
        }

        if (!$select->top && $place->top) {
            return Response::json([
                'status_code' => 404,
                'message' => '普通记录不允许移至置顶位置',
            ]);
        }

        $sort = $place->sort;
        try {
            if ($move_down) {
                //下移
                //增加移动区间的排序值
                self::where('sort', '>=', $place->sort)
                    ->where('sort', '<', $select->sort)
                    ->increment('sort');
            } else {
                //上移
                //减少移动区间的排序值
                self::where('sort', '>', $select->sort)
                    ->where('sort', '<=', $place->sort)
                    ->decrement('sort');
            }
        } catch (Exception $e) {
            return Response::json([
                'status_code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
        $select->sort = $sort;
        $select->save();

        return Response::json([
            'status_code' => 200,
            'message' => 'success',
        ]);
    }

    public static function state($input)
    {
        $ids = $input['ids'];
        $state = $input['state'];

        $items = static::whereIn('id', $ids)
            ->get();
        foreach ($items as $item) {
            $item->state = $state;
            $item->save();
            if ($state == static::STATE_DELETED) {
                $item->delete();
            } else if ($item->trashed()) {
                $item->restore();
            }
            if ($state == static::STATE_PUBLISHED) {
                $item->save();
            }
        }
    }

}