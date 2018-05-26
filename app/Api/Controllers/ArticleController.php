<?php

namespace App\Api\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Request;
use Exception;

class ArticleController extends BaseController
{
    public function __construct()
    {
    }

    public function transform($article)
    {
        $attributes = $article->getAttributes();
        $attributes['images'] = $article->images()->transform(function ($item) use ($article) {
            return [
                'id' => $item->id,
                'title' => !empty($item->title) ?: $article->title,
                'url' => get_file_url($item->url),
                'summary' => $item->summary,
            ];
        });
        $attributes['comment_count'] = $article->comment_count;
        $attributes['favorite_count'] = $article->favorite_count;
        $attributes['follow_count'] = $article->follow_count;
        $attributes['like_count'] = $article->like_count;
        $attributes['click_count'] = $article->click_count;
        $attributes['created_at'] = empty($article->created_at) ? '' : $article->created_at->toDateTimeString();
        $attributes['updated_at'] = empty($article->updated_at) ? '' : $article->updated_at->toDateTimeString();
        return $attributes;
    }

    /**
     * @SWG\Get(
     *   path="/article/lists",
     *   summary="获取文章列表",
     *   tags={"/article 文章"},
     *   @SWG\Parameter(name="category_id", in="query", required=true, description="栏目ID", type="string"),
     *   @SWG\Parameter(name="limit", in="query", required=false, description="分页大小", type="integer"),
     *   @SWG\Parameter(name="last_id", in="query", required=false, description="最后一个id", type="integer"),
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
        $filter['last_id'] = Request::get('last_id') ?: 0;
        $filter['limit'] = Request::get('limit') ?: 20;
        $filter['type'] = Request::get('type') ?: 0;

        //参数校验
        try {
            $check = $this->dataCheck($filter);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "article-list-" . $filter['type'] . "-" . $filter['last_id'] . "-" . $filter['limit'];
        return cache_remember($key, 1, function () use ($filter) {
            $articles = Article::filter($filter)
                ->where('state', Article::STATE_PUBLISHED)
                ->where('type', $filter['type'])
                ->select('id', 'title', 'summary', 'image_url', 'comment_num', 'view_num', 'published_at')
                ->orderBy('sort', 'desc')
                ->limit($filter['limit'])
                ->get();

            if (!empty($articles)) {
                $articles->transform(function ($article) {
                    $attribute = $article->getAttributes();
                    $attribute['image_url'] = get_file_url($article->image_url) . '/thumbnail';
                    return $attribute;
                });

                return $this->responseSuccess($articles);
            } else {
                return $this->responseError('没有找到文章', 404);
            }
        });
    }

    /**
     * @SWG\Get(
     *   path="/article/detail",
     *   summary="获取文章详情页",
     *   tags={"/article 文章"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="文章ID", type="string"),
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
        $input['limit'] = Request::get('limit') ?: 10;

        try {
            $check = $this->dataCheck($input);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $article = Article::find($input['id']);

        if ($article) {
            $data['id'] = $input['id'];
            $data['title'] = $article->title;
            $data['content'] = $article->content;
            $data['comment_num'] = $article->comment_num;
            $data['view_num'] = $article->view_num;
            $data['published_at'] = $article->published_at->toDateTimeString();
            $article->increment('view_num');
            //获取前10条评论
            $data['comments'] = $article->comments()
                ->filter($input)
                ->select('id', 'content', 'member_id', 'to_member_id', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit($input['limit'])
                ->get();

            foreach ($data['comments'] as $key => $comment) {
                $comment['member'] = $comment->member()->select('id', 'name', 'avatar_url', 'type')->first();
                $comment['to_member'] = $comment->toMember()->select('id', 'name', 'avatar_url', 'type')->first();
            }

            return $this->responseSuccess($data);

        } else {
            return $this->responseError('未找到资讯信息', 404);
        }
    }


    /**
     * @SWG\Get(
     *   path="/article/comment/add",
     *   summary="发表资讯评论",
     *   tags={"/article 文章"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="文章ID", type="string"),
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
    public function comment()
    {
        $filter['content'] = Request::get('content');
        $filter['id'] = Request::get('id');
        $filter['parent_id'] = Request::get('parent_id') ?: 0;
        $filter['to_member_id'] = Request::get('to_member_id') ?: 0;

        //参数校验
        try {
            $check = $this->dataCheck($filter);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
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


        $article = Article::find($filter['id']);

        if ($article) {
            $comment = $article->comments()->create([
                'state' => Comment::STATE_PASSED,
                'content' => $filter['content'],
                'member_id' => $member->id,
                'parent_id' => $filter['parent_id'],
                'to_member_id' => $filter['to_member_id'],
            ]);

            $data['comment_id'] = $comment->id;
            $data['article_id'] = $filter['id'];
            $data['content'] = $filter['content'];
            $data['parent_id'] = $filter['parent_id'];
            $data['member_id'] = $comment->member_id;
            $data['to_member_id'] = $filter['to_member_id'];
            $data['created_at'] = $comment->created_at->toDateTimeString();

            $data['member'] = $comment->member()->select('id', 'name', 'avatar_url', 'sex as gender')->first();
            $data['to_member'] = $comment->toMember()->select('id', 'name', 'avatar_url', 'sex as gender')->first();

            return $this->responseSuccess($data);

        } else {
            return $this->responseError('文章不存在', 404);
        }
    }

    /**
     * @SWG\Get(
     *   path="/article/comment/lists",
     *   summary="获取文章评论",
     *   tags={"/article 文章"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="文章ID", type="string"),
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
    public function comments()
    {
        $filter['id'] = Request::get('id');
        $filter['limit'] = Request::get('limit') ?: 20;
        $filter['last_id'] = Request::get('last_id') ?: 0;

        //参数校验
        try {
            $check = $this->dataCheck($filter);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $articles = Article::find($filter['id']);
        if ($articles) {
            $comments = $articles->comments()
                ->filter($filter)
                ->select('id as comment_id', 'content', 'member_id', 'to_member_id', 'created_at')
                ->orderBy('id', 'desc')
                ->limit($filter['limit'])
                ->get();

            //获取讨论点赞数
            foreach ($comments as $key => $comment) {
                $comment->member = $comment->member()->select('id', 'name', 'avatar_url', 'sex as gender')->first();
                $comment->to_member = $comment->toMember()->select('id', 'name', 'avatar_url', 'sex as gender')->first();
            }

            return $this->responseSuccess($comments);
        } else {
            return $this->responseError('文章不存在', 404);

        }


    }

}