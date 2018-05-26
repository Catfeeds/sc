<?php

namespace App\Models;

use Exception;
use Request;
use Response;

class Tag extends BaseModule
{
    const RECOMMEND = 1;

    const TYPE_IMG = 0;

    const TYPE = [
        '0' => '图片标签',
        '1' => '文章标签',
    ];

    protected $fillable = [
        'name',
        'type',
        'sort',
    ];

    const MODEL_CLASS = 'App\Models\Tag';

    protected $entities = ['type'];


    public function images()
    {
        return $this->belongsToMany(Image::class);
    }

    public function setCreatedAt($value)
    {
        $this->attributes['sort'] = strtotime($value);
        return parent::setCreatedAt($value);
    }

    public function scopeFilter($query, $filters)
    {
        $query->where(function ($query) use ($filters) {
            empty($filters['name']) ?: $query->where('name', $filters['name']);
            empty($filters['start_date']) ?: $query->where('created_at', '>=', $filters['start_date']);
            empty($filters['end_date']) ?: $query->where('created_at', '<=', $filters['end_date']);
            empty($filters['user_id']) ?: $query->whereHas('refer.user', function ($query) use ($filters) {
                $query->where('id', $filters['user_id']);
            });
            empty($filters['title']) ?: $query->whereHas('refer', function ($query) use ($filters) {
                $query->where('title', $filters['title']);
            });
        });
    }

    public static function lists($class)
    {
        return Tag::select('name', DB::raw('count(*) as total'))
            ->where('refer_type', $class)
            ->groupBy('name')
            ->get();
    }

    public static function stores($input)
    {
        $moment = static::create($input);

        //保存图片集
        if (isset($input['images']) && !empty($input['images'])) {
            Item::sync(Item::TYPE_IMAGE, $moment, $input['images']);
        }

        return $moment;
    }

    public static function updates($id, $input)
    {
        $tag = static::find($id);

        $tag->update($input);

        return $tag;
    }

    /**
     * tags
     */
    public static function tags($type)
    {
        $tags = static::where('type', $type)
            ->select('id', 'name', 'type')
            ->orderBy('id', 'desc')
            ->get();
        return $tags;
    }

    /**
     * table
     */
    public static function table()
    {
        $filters = Request::all();

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;

        $ds = new DataSource();
        $tags = static::filter($filters)
            ->orderBy('sort', 'desc')
            ->skip($offset)
            ->limit($limit)
            ->get();

        $ds->total = static::filter($filters)
            ->count();

        $tags->transform(function ($tag) {
            $attributes = $tag->getAttributes();

            //实体类型
            foreach ($tag->entities as $entity) {
                $entity_map = str_replace('type', 'type_name', $entity);
                $entity = str_replace('type', 'type', $entity);
                $attributes[$entity_map] = Tag::TYPE[$tag->$entity];
            }
            //日期类型
            foreach ($tag->dates as $date) {
                $attributes[$date] = empty($tag->$date) ? '' : $tag->$date->toDateTimeString();
            }
            $attributes['created_at'] = empty($tag->created_at) ? '' : $tag->created_at->toDateTimeString();
            $attributes['updated_at'] = empty($tag->updated_at) ? '' : $tag->updated_at->toDateTimeString();
            return $attributes;
        });

        $ds->rows = $tags;

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
                self::where('name', $select->name)
                    ->where('sort', '>=', $place->sort)
                    ->where('sort', '<', $select->sort)
                    ->increment('sort');
            } else {
                //上移
                //减少移动区间的排序值
                self::where('name', $select->name)
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

    public static function state($input)
    {
        $ids = $input['ids'];
        $state = $input['state'];

        $tags = static::whereIn('id', $ids)
            ->get();
        foreach ($tags as $tag) {
            $tag->save();
            $tag->state = $state;

            if ($state == static::STATE_DELETED) {
                $tag->delete();
            }
        }
    }
}