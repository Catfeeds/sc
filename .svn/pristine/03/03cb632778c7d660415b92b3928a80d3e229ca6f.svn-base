<?php

namespace App\Models;

use Exception;
use Request;
use Response;


class Gallery extends BaseModule
{
    const STATE_DELETED = 0;
    const STATE_NORMAL = 1;
    const STATE_CANCELED = 2;
    const STATE_PUBLISHED = 9;

    const SOURCE_MEMBER = 0;
    const SOURCE_USER = 1;

    const MODEL_CLASS = 'App\Models\Gallery';

    const STATES = [
        0 => '已删除',
        1 => '未发布',
        2 => '已撤回',
        9 => '已发布',
    ];

    const SOURCES = [
        0 => '用户',
        1 => '管理员',
    ];

    const STATE_PERMISSIONS = [
        0 => '@gallery-delete',
        2 => '@gallery-cancel',
        9 => '@gallery-publish',
    ];

    protected $table = 'galleries';

    protected $fillable = ['category_id', 'name', 'subname', 'intro', 'cover', 'source', 'pic_num', 'collect_num', 'uploader_id', 'sort', 'state', 'published_at'];

    protected $dates = ['published_at'];

    protected $entities = [];


    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function getSourceName()
    {
        return array_key_exists($this->source, static::SOURCES) ? static::SOURCES[$this->source] : '';
    }

    public function previous()
    {
        return static::where('category_id', $this->category_id)
            ->where('state', $this->state)
            ->where('sort', '>', $this->sort)
            ->first();
    }

    public function next()
    {
        return static::where('category_id', $this->category_id)
            ->where('state', $this->state)
            ->where('sort', '<', $this->sort)
            ->first();
    }

    public static function stores($input)
    {
        $input['state'] = static::STATE_NORMAL;

        $gallery = static::create($input);

        return $gallery;
    }

    public static function updates($id, $input)
    {
        $gallery = static::find($id);
        $input['source'] = array_search($input['source'], static::SOURCES);

        $gallery->update($input);

        return $gallery;
    }

    public static function table()
    {
        $filters = Request::all();

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;

        $ds = new DataSource();
        $galleries = static::with('member', 'user')
            ->filter($filters)
            ->orderBy('sort', 'desc')
            ->skip($offset)
            ->limit($limit)
            ->get();

        $ds->total = static::filter($filters)
            ->count();

        $galleries->transform(function ($gallery) {
            $attributes = $gallery->getAttributes();

            //实体类型
            foreach ($gallery->entities as $entity) {
                $entity_map = str_replace('_id', '_name', $entity);
                $entity = str_replace('_id', '', $entity);
                $attributes[$entity_map] = empty($gallery->$entity) ? '' : $gallery->$entity->name;
            }

            //日期类型
            foreach ($gallery->dates as $date) {
                $attributes[$date] = empty($gallery->$date) ? '' : $gallery->$date->toDateTimeString();
            }

            $attributes['uploader_id'] = ($attributes['source'] == static::SOURCE_MEMBER) ? Member::find($attributes['uploader_id'])->name : User::find($attributes['uploader_id'])->name;
            $attributes['source'] = $gallery->getSourceName();
            $attributes['state_name'] = $gallery->stateName();
            $attributes['created_at'] = empty($gallery->created_at) ? '' : $gallery->created_at->toDateTimeString();
            $attributes['updated_at'] = empty($gallery->updated_at) ? '' : $gallery->updated_at->toDateTimeString();
            return $attributes;
        });

        $ds->rows = $galleries;

        return Response::json($ds);
    }

    /**
     * 排序
     */
    public static function sort()
    {
        var_dump(Request::all());
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
                self::where('category_id', $select->category_id)
                    ->where('sort', '>=', $place->sort)
                    ->where('sort', '<', $select->sort)
                    ->increment('sort');
            } else {
                //上移
                //减少移动区间的排序值
                self::where('category_id', $select->category_id)
                    ->where('sort', '>', $select->sort)
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
}