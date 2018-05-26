<?php

namespace App\Api\Controllers;

use App\Models\Gallery;
use App\Models\Image;
use App\Models\Item;
use App\Models\Tag;
use function foo\func;
use Request;
use App\Models\Member;
use Exception;

class ImageController extends BaseController
{
    public function __construct()
    {
    }

    public function transform($image)
    {
        $member = null;
        if (\JWTAuth::getToken()) {
            try {
                $member = \JWTAuth::parseToken()->authenticate();
            } catch (Exception $e) {

            }
        }
        if ($member) {
            $image->liked = in_array($image->id, $member->likes()->pluck('refer_id')->toArray()) ? 1 : 0;
        } else {
            $image->liked = 0;
        }

        $image->images()->transform(function ($item) use ($image) {

            $item->url = get_file_url($item->url);
            $item->w = $item->w;
            $item->h = $item->h;

            $image->origin = $item;
            //缩略图
            $thumbnail = new Item();
            $thumbnail->url = get_file_url($item->url) . '/thumbnail';
            $thumbnail->w = $item->w;
            $thumbnail->h = $item->h;

            $image->thumbnail = $thumbnail;

            return $image;
        });

        $attributes = $image->getAttributes();

        return $attributes;
    }

    /**
     * @SWG\Get(
     *   path="/gallery/image/lists",
     *   summary="获取图片列表",
     *   tags={"/images 图片"},
     *   @SWG\Parameter(name="last_id", in="query", required=true, description="最后一条图片记录id", type="integer"),
     *   @SWG\Parameter(name="limit", in="query", required=true, description="显示数量", type="integer"),
     *   @SWG\Parameter(name="id", in="query", required=true, description="图集id", type="integer"),
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
        $input['last_id'] = Request::get('last_id') ?: 0;
        $input['limit'] = Request::get('limit') ?: 1;
        $input['id'] = Request::get('id');

        //参数校验
        try {
            $check = $this->dataCheck($input);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $gallery = Gallery::find($input['id']);

        $gallery->pic_num = $gallery->images()->count();
        $key = "image-list-" . $input['last_id'] . "-" . $input['limit'];

        return cache_remember($key, 1, function () use ($input, $gallery) {
            $images = Image::with('items')
                ->where('state', Image::STATE_PUBLISHED)
                ->filter($input)
                ->orderBy('sort', 'desc')
                ->limit($input['limit'])
                ->select('id', 'like_num', 'view_num')
                ->get();

            $images->transform(function ($image) {
                return $this->transform($image);
            });

            $data['intro'] = $gallery->intro;
            $data['count'] = $gallery->pic_num;
            $data['images'] = $images;
            return $this->responseSuccess($data);
        });
    }

    /**
     * @SWG\Get(
     *   path="/gallery/image/explore",
     *   summary="获取图片列表",
     *   tags={"/images 图片"},
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
    public function explore()
    {
        $filters['limit'] = Request::get('limit') ? Request::get('limit') : 20;
        $filters['last_id'] = Request::get('last_id') ? Request::get('last_id') : 0;

        //参数校验
        try {
            $check = $this->dataCheck($filters);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "image-list-" . $filters['limit'] . "-" . $filters['last_id'];

        return cache_remember($key, 1, function () use ($filters) {
            $images = Image::where('state', Image::STATE_PUBLISHED)
                ->orderBy('sort', 'desc')
                ->filter($filters)
                ->limit($filters['limit'])
                ->get();

            $images->transform(function ($image) {
                return $this->transform($image);
            });

            return $this->responseSuccess($images);
        });
    }

    /**
     * @SWG\Get(
     *   path="/gallery/image/relate",
     *   summary="获取图片列表",
     *   tags={"/images 图片"},
     *   @SWG\Parameter(name="page_size", in="query", required=true, description="分页大小", type="integer"),
     *   @SWG\Parameter(name="page", in="query", required=true, description="分页序号", type="integer"),
     *   @SWG\Parameter(name="id", in="query", required=true, description="图片id", type="integer"),
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
    public function relate()
    {
        $filters['limit'] = Request::get('limit') ? Request::get('limit') : 20;
        $filters['last_id'] = Request::get('last_id') ? Request::get('last_id') : 0;
        $filters['id'] = Request::get('id');

        //参数校验
        try {
            $check = $this->dataCheck($filters);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "image-list-" . $filters['limit'] . "-" . $filters['last_id'] . "-" . $filters['id'];

        //todo 根据相关算法查询与此图片相关的图片列表
        return cache_remember($key, 1, function () use ($filters) {

            $images = Image::with('items')
                ->select('id', 'title', 'like_num', 'view_num')
                ->where('state', Image::STATE_PUBLISHED)
                ->filter($filters)
                ->orderBy('sort', 'desc')
                ->limit($filters['limit'])
                ->get();

            $images->transform(function ($image) {
                return $this->transform($image);
            });

            return $this->responseSuccess($images);
        });
    }

    /**
     * @SWG\Get(
     *   path="/gallery/search",
     *   summary="搜索图片",
     *   tags={"/images 图片"},
     *   @SWG\Parameter(name="content", in="query", required=true, description="搜索内容", type="string"),
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
    public function search()
    {
        $input['content'] = Request::get('content');

        //图集搜索
        $galleries = Gallery::where('intro', 'like', '%' . $input['content'] . '%')
            ->where('state', Image::STATE_PUBLISHED)
            ->select('id', 'name', 'cover', 'uploader_id', 'pic_num', 'collect_num')
            ->orderBy('sort', 'desc')
            ->get();

        $galleries->transform(function ($gallery) {
            $gallery->cover = get_file_url($gallery->cover);
            $gallery->uploader = Member::where('id', $gallery->uploader_id)->select('id', 'nick_name', 'avatar_url')->first();
            if ($gallery->uploader) {
                $gallery->uploader->avatar_url = get_file_url($gallery->uploader->avatar_url);
            }
            $images = $gallery->images()->select('id', 'title')->where('is_cover', Image::TYPE_COVER)->limit(3)->get()->transform(function ($image) {
                $image = $this->transform($image);

                return $image['thumbnail']->url . '/thumbnail';
            });
            $gallery->images = $images;

            $gallery = $gallery->getAttributes();
            return $gallery;
        });

        $gallery_num = Gallery::where('intro', 'like', '%' . $input['content'] . '%')
            ->where('state', Image::STATE_PUBLISHED)
            ->count();

        //图片搜索
        $images = Image::with('items')
            ->where('intro', 'like', '%' . $input['content'] . '%')
            ->where('state', Image::STATE_PUBLISHED)
            ->select('id', 'title', 'author', 'like_num', 'view_num', 'created_at')
            ->orderBy('sort', 'desc')
            ->get();

        $image_num = Image::with('items')
            ->where('intro', 'like', '%' . $input['content'] . '%')
            ->where('state', Image::STATE_PUBLISHED)
            ->count();

        $images->transform(function ($image) {
            return $this->transform($image);
        });

        $data['gallery']['total'] = $gallery_num;
        $data['gallery']['data'] = $galleries;
        $data['image']['total'] = $image_num;
        $data['image']['data'] = $images;

        if (!empty($image_num) || !empty($gallery_num)) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError('搜索失败,未找到内容');
        }
    }


    /**
     * @SWG\Get(
     *   path="/gallery/search/tag",
     *   summary="搜索标签下的图片",
     *   tags={"/gallery 图库"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="标签id", type="string"),
     *   @SWG\Parameter(name="limit", in="query", required=true, description="分页大小", type="integer"),
     *   @SWG\Parameter(name="start", in="query", required=true, description="分页序号", type="integer"),
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
    public function tag()
    {
        $page_size = Request::get('page_size') ? Request::get('page_size') : 20;
        $page = Request::get('page') ? Request::get('page') : 1;
        $id = Request::get('id');

        $tag = Tag::find($id);
        if (!$tag) {
            return $this->responseError('标签不存在');
        }

        $images = $tag->images()->select('id', 'like_num', 'view_num')->get();
        if ($images) {
            $images->transform(function ($image) {
                return $this->transform($image);
            });

            return $this->responseSuccess($images);
        } else {
            return $this->responseError('该标签下暂无图片');
        }

    }

    /**
     * @SWG\Get(
     *   path="/gallery/image/detail",
     *   summary="获取图片详情页",
     *   tags={"/images 图片"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="图片ID", type="string"),
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
    public function detail()
    {
        $input['id'] = Request::get('id');

        //参数校验
        try {
            $check = $this->dataCheck($input);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $data = Image::find($input['id']);

        if (empty($data)) {
            return $this->responseError('图片不存在', 404);
        }
        //增加浏览量
        $data->increment('view_num');

        $gallery = $data->gallery();

        $data->item = $data->items()->where('type', Item::TYPE_IMAGE)->select('id', 'url', 'w', 'h', 'size')->first();
        $data->item->url = get_file_url($data->item->url);
        $thumb = $data->items()->where('type', Item::TYPE_IMAGE)->select('id', 'url', 'w', 'h', 'size')->first();
        $thumb->url = get_file_url($thumb->url) . '/thumbnail';
        $data->thumbnail = $thumb;
        //todo 标签对象待添加

        $data->gallery = $gallery->select(['id', 'cover', 'name'])->first();
        $data->gallery->cover = get_file_url($data->gallery->cover);

        if ($gallery->first()->source == Gallery::SOURCE_MEMBER) {
            $data->uploader = Member::find($data->uploader_id, ['id', 'name', 'avatar_url']);
            $data->uploader->aratar_url = get_file_url($data->uploader->aratar_url);
        }

        return $this->responseSuccess($data);

    }

    /**
     * @SWG\Get(
     *   path="/gallery/image/like",
     *   summary="点赞喜欢的图片",
     *   tags={"/galleries 图库"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="图片ID", type="string"),
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
    public function like()
    {
        $input = Request::all();

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError('无效的token,请重新登录', 401);
        }

        //参数校验
        try {
            $this->dataCheck($input);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }


        $image = Image::find($input['id']);

        if (!$image) {
            return $this->responseError('图片不存在', 404);
        }

        $like = $image->likes()->where('member_id', $member->id)->first();

        if ($like) {
            return $this->responseError('已点赞，请勿重复点赞！');
        }

        $re = $image->likes()->create([
            'member_id' => $member->id,
        ]);

        $res = $image->increment('like_num');


        if ($re && $res) {
            $data['like_num'] = $image['like_num'];
            return $this->responseSuccess($data, '点赞成功');
        } else {
            return $this->responseError('点赞失败');
        }
    }

    /**
     * @SWG\Post(
     *   path="/gallery/image/dislike",
     *   summary="取消点赞喜欢的图片",
     *   tags={"/galleries 图库"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="图库ID", type="string"),
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
    public function dislike()
    {
        $input = Request::input();

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError('无效的token,请重新登录', 401);
        }

        try {
            $this->dataCheck($input);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $image = Image::find($input['id']);

        if (!$image) {
            return $this->responseError('图片不存在', 404);
        }

        $delLike = $image->likes()->where('member_id', $member->id)->delete();

        if ($delLike) {
            $image->decrement('like_num');

            $data['like_num'] = $image['like_num'];
            return $this->responseSuccess($data, '取消点赞成功');
        } else {
            return $this->responseError('取消点赞失败');
        }
    }
}