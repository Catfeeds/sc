<?php

namespace App\Models;

use Exception;
use Request;
use Response;

class Article extends BaseModule
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
        0 => '@article-delete',
        2 => '@article-cancel',
        9 => '@article-publish',
    ];

    const MODEL_CLASS = 'App\Models\Article';

    protected $table = 'articles';

    protected $fillable = ['category_id', 'type', 'title', 'subtitle', 'author', 'origin', 'tags', 'summary', 'image_url', 'video_url', 'content', 'top', 'user_id', 'sort', 'state', 'published_at'];

    protected $dates = ['published_at'];

    protected $entities = ['member_id', 'user_id'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public static function stores($input)
    {
        $input['state'] = static::STATE_NORMAL;

        $article = static::create($input);

        return $article;
    }

    public static function updates($id, $input)
    {
        $article = static::find($id);

        $article->update($input);

        return $article;
    }

    public static function table()
    {
        $filters = Request::all();

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;

        $ds = new DataSource();
        $articles = static::with('member', 'user')
            ->filter($filters)
            ->orderBy('top', 'desc')
            ->orderBy('sort', 'desc')
            ->skip($offset)
            ->limit($limit)
            ->get();


        $ds->total = static::filter($filters)
            ->count();

        $articles->transform(function ($article) {
            $attributes = $article->getAttributes();

            //实体类型
            foreach ($article->entities as $entity) {
                $entity_map = str_replace('_id', '_name', $entity);
                $entity = str_replace('_id', '', $entity);
                $attributes[$entity_map] = empty($article->$entity) ? '' : $article->$entity->name;
            }

            //日期类型
            foreach ($article->dates as $date) {
                $attributes[$date] = empty($article->$date) ? '' : $article->$date->toDateTimeString();
            }
            $attributes['state_name'] = $article->stateName();
            $attributes['created_at'] = empty($article->created_at) ? '' : $article->created_at->toDateTimeString();
            $attributes['updated_at'] = empty($article->updated_at) ? '' : $article->updated_at->toDateTimeString();
            return $attributes;
        });

        $ds->rows = $articles;
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