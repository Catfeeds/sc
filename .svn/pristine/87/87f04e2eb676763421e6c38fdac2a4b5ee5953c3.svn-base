<?php

namespace App\Api\Controllers;

use App\Models\Gallery;
use App\Models\Image;
use App\Models\Item;
use Request;
use Exception;

class GalleryController extends BaseController
{
    public function __construct()
    {
    }

    public function transform($gallery)
    {
        $member = null;
        if (\JWTAuth::getToken()) {
            try {
                $member = \JWTAuth::parseToken()->authenticate();
            } catch (Exception $e) {

            }
        }
        if ($member) {
            $gallery->is_collected = in_array($member->id, $gallery->favorites()->pluck('member_id')->toArray()) ? 1 : 0;
        } else {
            $gallery->is_collected = 0;
        }

        $gallery->cover = get_file_url($gallery->cover);
        $attributes = $gallery->getAttributes();

        $attributes['pic_num'] = $gallery->pic_num;
        $attributes['collect_num'] = $gallery->collect_num;
        $attributes['created_at'] = empty($gallery->created_at) ? '' : $gallery->created_at->toDateTimeString();
        $attributes['updated_at'] = empty($gallery->updated_at) ? '' : $gallery->updated_at->toDateTimeString();
        return $attributes;
    }

    /**
     * @SWG\Get(
     *   path="/gallery/lists",
     *   summary="获取图集列表",
     *   tags={"/galleries 图集"},
     *   @SWG\Parameter(name="category_id", in="query", required=true, description="栏目ID", type="string"),
     *   @SWG\Parameter(name="page_size", in="query", required=true, description="分页大小", type="integer"),
     *   @SWG\Parameter(name="page", in="query", required=true, description="分页序号", type="integer"),
     *   @SWG\Response(
     *     response=200,
     *     description="查询成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到"
     *   )
     * )
     */
    public function lists()
    {
        $filters['category_id'] = Request::get('category_id');
        $filters['last_id'] = Request::get('last_id') ? Request::get('last_id') : 0;
        $filters['limit'] = Request::get('limit') ? Request::get('limit') : 20;

        //参数校验
        try {
            $check = $this->dataCheck($filters);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "gallery-list-" . $filters['category_id'] . "-" . $filters['limit'] . "-" . $filters['last_id'];

        return cache_remember($key, 1, function () use ($filters) {
            $galleries = Gallery::where('category_id', $filters['category_id'])
                ->where('state', Gallery::STATE_PUBLISHED)
                ->filter($filters)
                ->orderBy('sort', 'desc')
                ->limit($filters['limit'])
                ->get();

            if ($galleries->count() == 0) {
                return $this->responseError('未找到该图集分类');
            }

            $galleries->transform(function ($gallery) {
                $images = $gallery->images()->where('is_cover', Image::TYPE_COVER)->select('id')->get();
                $gallery->images = array();
                foreach($images as $image){
                    $arr[] = get_file_url($image->items()->where('type', Item::TYPE_IMAGE)->pluck('url')->first()) . '/thumbnail';
                }
                $gallery->images = $arr;

                return $this->transform($gallery);
            });

            return $this->responseSuccess($galleries);
        });
    }

    /**
     * @SWG\Post(
     *   path="/gallery/collect",
     *   summary="图集收藏",
     *   tags={"/galleries 图集"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="图集ID", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="查询成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到"
     *   )
     * )
     */
    public function collect()
    {
        $input = Request::input();

        //参数校验
        try {
            $this->dataCheck($input);

        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 401);
        }

        $gallery = Gallery::find($input['id']);

        if (empty($gallery)) {
            return $this->responseError('图集不存在', 404);
        }

        $count = $gallery->favorites()->where('member_id', $member->id)->count();
        if ($count) {
            return $this->responseError('请勿重复收藏图集');
        }
        $re = $gallery->favorites()->create([
            'member_id' => $member->id
        ]);

        $res = $gallery->increment('collect_num');

        if ($re && $res) {
            $data['collect_num'] = $gallery['collect_num'];

            return $this->responseSuccess($data, '图集收藏成功');
        }

    }


    /**
     * @SWG\Post(
     *   path="/gallery/uncollect",
     *   summary="取消收藏图集",
     *   tags={"/galleries 图集"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="图集ID", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="查询成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到"
     *   )
     * )
     */
    public function uncollect()
    {
        $input = Request::input();

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 401);
        }

        //参数校验
        try {
            $this->dataCheck($input);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $gallery = Gallery::find($input['id']);

        if (!$gallery) {
            return $this->responseError('图集不存在', 404);
        }

        $re = $gallery->favorites()->where('member_id', $member->id)->delete();
        if ($re) {
            if ($gallery->collect_num != 0) {
                $gallery->decrement('collect_num');
            }

            $data['collect_num'] = $gallery['collect_num'];

            return $this->responseSuccess($data, '取消收藏图集成功');
        } else {
            return $this->responseError('取消收藏图集失败');
        }
    }
}