<?php

namespace App\Api\Controllers;

use App\Models\Comment;
use App\Models\Member;
use App\Models\Moment;
use App\Models\Item;
use App\Models\Report;
use Request;
use Exception;
use DB;

class MomentController extends BaseController
{
    protected $module;

    public function __construct()
    {
        $this->module = $this->getModule(__CLASS__);
    }

    public function transform($moment)
    {
        $member2 = null;
        if (\JWTAuth::getToken()) {
            try {
                $member2 = \JWTAuth::parseToken()->authenticate();
            } catch (Exception $e) {

            }
        }
        $member = $moment->member()->first();
        if ($member2) {
            $moment->is_like = in_array($member2->id, $moment->likes()->pluck('member_id')->toArray()) ? 1 : 0;
            $member->is_followed = empty($member2->follows()->where('member_id', $moment->member_id)->first()) ? 0 : 1;
        } else {
            $moment->is_like = 0;
            $member->is_followed = 0;
        }

        $attributes = $moment->getAttributes();

        if ($attributes['type'] == Moment::TYPE_IMAGES) {
            //获取动态图片
            $images = $moment->items()->where('type', Item::TYPE_IMAGE)->pluck('url');
            foreach ($images as $key => $val) {
                $images[$key] = get_file_url($images[$key]);
            }

            $thumbnails = $moment->items()->where('type', Item::TYPE_IMAGE)->pluck('url');
            foreach ($thumbnails as $key => $val) {
                $thumbnails[$key] = get_file_url($thumbnails[$key]) . '/thumbnail';
            }
        } else {
            $images = $thumbnails = [];
        }

        $member_detail = $member->detail()->first();

        $attributes['images'] = $images;
        $attributes['thumbnails'] = $thumbnails;
        $attributes['member']['id'] = $member->id;
        $attributes['member']['name'] = $member->nick_name;
        $attributes['member']['type'] = $member->type;
        $attributes['member']['is_followed'] = $member->is_followed;
        $attributes['member']['city'] = empty($member_detail) ? '' : $member_detail->getCityAttribute();
        $attributes['member']['avatar_url'] = get_file_url($member->avatar_url);
        $attributes['member']['sex'] = $member->sex;
        $attributes['member']['state'] = $member->state;
        $attributes['member']['title'] = $member->title;

        $attributes['created_at'] = empty($moment->created_at) ? '' : $moment->created_at->toDateTimeString();
        $attributes['updated_at'] = empty($moment->updated_at) ? '' : $moment->updated_at->toDateTimeString();
        return $attributes;
    }

    /**
     * @SWG\Get(
     *   path="/moment/lists",
     *   summary="获取动态列表",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="limit", in="query", required=true, description="分页大小", type="integer"),
     *   @SWG\Parameter(name="start", in="query", required=true, description="偏移量", type="integer"),
     *   @SWG\Parameter(name="member_id", in="query", required=true, description="会员id", type="integer"),
     *   @SWG\Parameter(name="sort", in="query", required=true, description="排序方式（0最新 1最热：涉及最热算法）", type="integer"),
     *   @SWG\Parameter(name="last_id", in="query", required=true, description="最后一条动态id", type="integer"),
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
        $filter['member_id'] = Request::get('member_id') ? Request::get('member_id') : 0;
        $filter['limit'] = Request::get('limit') ? Request::get('limit') : 20;
        $filter['sort'] = Request::get('sort') ? Request::get('sort') : 0;
        $filter['start'] = Request::get('start') ? Request::get('start') : 1;
        $filter['topic'] = Request::get('topic') ?: '';
        $filter['last_id'] = Request::get('last_id');

        try {
            //参数校验
            $filter = $this->dataCheck($filter);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "moment-list-" . $filter['member_id'] . "-" . $filter['limit'] . "-" . $filter['last_id'];

        if ($filter['sort'] == Moment::SORT_TIME) {
            return cache_remember($key, 1, function () use ($filter) {

                $moments = Moment::where('state', Moment::STATE_PUBLISHED)
                    ->filter($filter)
                    ->orderBy('id', 'desc')
                    ->limit($filter['limit'])
                    ->get();

                $moments->transform(function ($moment) {
                    //获取点赞信息
                    $likes = $moment->likes()->select('member_id')->limit(Comment::DEFAULT_NUM)->get();
                    //处理点赞
                    $likes->transform(function ($like) {
                        $member = $like->member()->first();
                        $like->member_id = $member->id;
                        $like->avatar_url = get_file_url($member->avatar_url);
                        $like->name = $member->name;

                        return $like;
                    });
                    $moment->likes = $likes;

                    return $this->transform($moment);
                });

                return $this->responseSuccess($moments);
            });
        } else {

            $a = 1;
            $b = 1;
            $c = 1;

            //根据最热算法查询动态列表
            $moments = DB::select("select *,($a*comment_num+$b*like_num)/$c*(1+unix_timestamp(curdate())-unix_timestamp(DATE_FORMAT(created_at,'%Y-%m-%d'))) as moment_order from mk_moments order by moment_order desc limit " . ($filter['start'] - 1) * $filter['limit'] . "," . $filter['limit']);

            foreach ($moments as $key => $moment) {
                $moment = new Moment(object_to_array($moment));
                //获取点赞信息
                $likes = $moment->likes()->select('member_id')->limit(Comment::DEFAULT_NUM)->get();
                //处理点赞
                $likes->transform(function ($like) {
                    $member = $like->member()->first();
                    $like->member_id = $member->id;
                    $like->avatar_url = get_file_url($member->avatar_url);
                    $like->name = $member->name;

                    return $like;
                });
                $moment->likes = $likes;

                $moments[$key] = $this->transform($moment);
            }

            return $this->responseSuccess($moments);

        }

    }

    /**
     * @SWG\Post(
     *   path="/moment/add",
     *   summary="发布动态",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="content", in="query", required=true, description="动态内容", type="string"),
     *   @SWG\Parameter(name="images", in="query", required=true, description="图片集字符串", type="string"),
     *   @SWG\Parameter(name="location", in="query", required=true, description="发布地点", type="string"),
     *   @SWG\Parameter(name="topic_id", in="query", required=true, description="话题id", type="integer"),
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
    public function add()
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

        try {
            //参数校验
            $this->dataCheck($input);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        if (empty($input['images']) && empty($input['content'])) {
            return $this->responseError('动态内容不能为空');
        }

        $input['member_id'] = $member->id;

        if (isset($input['images']) && !empty($input['images'])) {
            $input['type'] = Moment::TYPE_IMAGES;
        } else {
            $input['type'] = Moment::TYPE_WORDS;
        }

        $moment = Moment::stores($input);

        if ($moment) {
            return $this->responseSuccess($moment, '动态发布成功');
        } else {
            return $this->responseError('动态发布失败');
        }

    }

    /**
     * @SWG\Post(
     *   path="/moment/comment/add",
     *   summary="添加动态评论",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="content", in="query", required=true, description="评论", type="string"),
     *   @SWG\Parameter(name="moment_id", in="query", required=true, description="动态id", type="integer"),
     *   @SWG\Parameter(name="parent_id", in="query", required=true, description="父评论id", type="integer"),
     *   @SWG\Parameter(name="to_member_id", in="query", required=true, description="回复会员id", type="integer"),
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

        $input = Request::input();

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 401);
        }

        try {
            //参数校验
            $this->dataCheck($input);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $moment = Moment::find($input['moment_id']);

        $comment = $moment->comments()->create([
            'content' => $input['content'],
            'parent_id' => $input['parent_id'],
            'member_id' => $member->id,
            'to_member_id' => isset($input['to_member_id']) ? $input['to_member_id'] : 0,
            'state' => Comment::STATE_PASSED,
        ]);

        if ($comment) {
            //当为一级评论的时候，自增动态评论数
            if ($input['parent_id'] == Comment::PARENT_ROOT) {
                $moment->increment('comment_num');
            }

            $comment->getAttributes();

            $member = $member->getAttributes();

            $res['id'] = $member['id'];
            $res['name'] = $member['name'];
            $res['avatar_url'] = $member['avatar_url'];
            $res['sex'] = $member['sex'];

            $comment['member'] = $res;

            if (isset($input['to_member_id'])) {
                $comment['to_member'] = Member::find($input['to_member_id'], ['id', 'name', 'avatar_url', 'sex'])->getAttributes();
            }

            $comment['sum'] = $moment['comment_num'];

            return $this->responseSuccess($comment, '动态评论成功');
        } else {
            return $this->responseError('动态评论失败');
        }
    }

    /**
     * @SWG\Post(
     *   path="/moment/detail",
     *   summary="动态详情",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="动态ID", type="integer"),
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

        try {
            //参数校验
            $check = $this->dataCheck($input);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $moment = Moment::find($input['id']);

        if (empty($moment)) {
            return $this->responseError('动态不存在', 404);
        }

        $comments = $moment->comments()->limit(Comment::DEFAULT_NUM)->select('id', 'member_id', 'content', 'to_member_id', 'created_at')->get();
        //处理评论
        $comments->transform(function ($comment) {
            //获取动态评论用户信息
            $comment->member = $comment->member()->select('id', 'name', 'avatar_url', 'sex')->first();
            if (!empty($comment->member)) {
                $comment->member->avatar_url = empty($comment->member->avatar_url) ?: get_file_url($comment->member->avatar_url);
            }
            //获取被回复用户信息
            $comment->to_member = $comment->toMember()->select('id', 'name', 'avatar_url', 'sex')->first();
            if (!empty($comment->to_member)) {
                $comment->to_member->avatar_url = empty($comment->to_member->avatar_url) ?: get_file_url($comment->to_member->avatar_url);
            }

            return $comment;
        });
        $moment->comments = $comments;
        //获取点赞信息
        $likes = $moment->likes()->select('member_id')->get();
        //处理点赞
        $likes->transform(function ($like) {
            $member = $like->member()->first();
            $like->member_id = $member->id;
            $like->avatar_url = get_file_url($member->avatar_url);
            $like->name = $member->name;

            return $like;
        });
        $moment->likes = $likes;

        $moment = $this->transform($moment);

        return $this->responseSuccess($moment);

    }

    /**
     * @SWG\Post(
     *   path="/moment/like",
     *   summary="动态点赞",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="动态ID", type="integer"),
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
        $input = Request::input();

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 401);
        }
        // 记录用户进入方法
        $this->enterClassLog($this->module, $member->mobile, __CLASS__, __FUNCTION__);

        try {
            $this->dataCheck($input);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $moment = Moment::find($input['id']);

        if (!$moment) {
            return $this->responseError('动态不存在', 404);
        }

        $like = $moment->likes()->where('member_id', $member->id)->first();

        if ($like) {
            return $this->responseError('已点赞，请勿重复点赞！');
        }

        try {
            //开始事务
            DB::beginTransaction();

            $moment->likes()->create(['member_id' => $member->id]);

            $moment->member_avatar = get_file_url($member->avatar_url);
            $moment->member_avatar_thumb = get_file_url($member->avatar_url) . '/thumbnail';

            $moment->increment('like_num');

            $this->log($this->module, '用户:' . $member->mobile . ' 点赞动态.', ['moment_id' => $input['id']]);

            DB::commit();

            return $this->responseSuccess($moment, '点赞成功');

        } catch (Exception $e) {
            DB::rollBack();
            // log error
            $this->log($this->module, '点赞失败', ['message' => $e->getMessage(), 'moment_id' => $input['id']], 'error');
            return $this->responseError('点赞失败');
        }
    }

    /**
     * @SWG\Post(
     *   path="/moment/dislike",
     *   summary="取消点赞动态",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="动态id", type="integer"),
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
            return $this->responseError($e->getMessage(), 401);
        }
        // log
        $this->enterClassLog($this->module, $member->mobile, __CLASS__, __FUNCTION__);

        try {
            $this->dataCheck($input);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $moment = Moment::find($input['id']);

        if (!$moment) {
            return $this->responseError('动态不存在', 404);
        }

        try {
            //开始事务
            DB::beginTransaction();

            $moment->likes()->where('member_id', $member->id)->delete();

            $moment->decrement('like_num');

            $this->log($this->module, '用户:' . $member->mobile . ' 取消点赞动态.', ['moment_id' => $input['id']]);
            DB::commit();

            return $this->responseSuccess($moment, '取消点赞动态成功');
        } catch (Exception $e) {
            DB::rollBack();
            // log error
            $this->log($this->module, '取消点赞动态失败', ['message' => $e->getMessage(), 'moment_id' => $input['id']], 'error');
            return $this->responseError('取消点赞动态失败');
        }
    }


    /**
     * @SWG\Get(
     *   path="/moment/comments",
     *   summary="获取动态评论列表",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="动态id", type="integer"),
     *   @SWG\Parameter(name="last_id", in="query", required=true, description="最后一条评论id", type="integer"),
     *   @SWG\Parameter(name="limit", in="query", required=true, description="分页大小", type="integer"),
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
        $filter['limit'] = Request::get('limit') ? Request::get('limit') : 20;
        $filter['last_id'] = Request::get('last_id');

        //参数校验
        try {
            $filter = $this->dataCheck($filter);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "moment-comment-lists-" . $filter['limit'] . "-" . $filter['last_id'];

        return cache_remember($key, 1, function () use ($filter) {
            $moment = Moment::find($filter['id']);

            $coments = $moment->comments()
                ->where('state', Comment::STATE_PASSED)
                ->filter($filter)
                ->select('id', 'member_id', 'content', 'to_member_id', 'created_at')
                ->orderBy('id', 'desc')
                ->limit($filter['limit'])
                ->get();

            $coments->transform(function ($comment) {
                //获取动态评论用户信息
                $comment->member = $comment->member()->select('id', 'name', 'avatar_url', 'sex')->first();
                if (!empty($comment->member)) {
                    $comment->member->avatar_url = empty($comment->member->avatar_url) ?: get_file_url($comment->member->avatar_url);
                }
                //获取被回复用户信息
                $comment->to_member = $comment->toMember()->select('id', 'name', 'avatar_url', 'sex')->first();
                if (!empty($comment->to_member)) {
                    $comment->to_member->avatar_url = empty($comment->to_member->avatar_url) ?: get_file_url($comment->to_member->avatar_url);
                }

                return $comment;
            });
            if (!empty($coments)) {
                return $this->responseSuccess($coments, '动态评论列表获取成功');
            } else {
                return $this->responseError('动态评论获取失败');
            }

        });

    }

    /**
     * @SWG\Post(
     *   path="/moment/report",
     *   summary="举报动态",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="动态id", type="integer"),
     *   @SWG\Parameter(name="content", in="query", required=true, description="举报内容", type="string"),
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
    public function report()
    {
        $filter = Request::input();

        //参数校验
        try {
            $filter = $this->dataCheck($filter);
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

        $filter['member_id'] = $member->id;

        $moment = Moment::find($filter['id']);

        if (empty($moment)) {
            return $this->responseError('动态不存在', 404);
        }

        $count = $moment->reports()->where('member_id', $filter['member_id'])->count();

        if ($count) {
            return $this->responseError('已举报成功，请勿重复举报');
        }

        $report = $moment->reports()->create([
            'content' => $filter['content'],
            'member_id' => $filter['member_id'],
            'state' => Report::STATE_NORMAL,
        ]);

        if ($report) {
            return $this->responseSuccess($report, '举报成功');
        } else {
            return $this->responseError('举报失败');
        }

    }

    /**
     * @SWG\Get(
     *   path="/moment/topic/lists",
     *   summary="话题列表",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="token", in="header", required=true, description="用户登录token", type="string"),
     *   @SWG\Parameter(name="topic", in="query", required=true, description="话题", type="string"),
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
    public function topics()
    {
        $filter['topic'] = Request::get('topic') ?: '';
        $filter['start'] = Request::get('start') ?: 0;
        $filter['limit'] = Request::get('limit') ?: 20;

        //参数校验
        try {
            $filter = $this->dataCheck($filter);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "moment-topic-lists-" . $filter['limit'];

        return cache_remember($key, 1, function () use ($filter) {

            $topics = DB::table('moments')
                ->select('id', DB::raw('count(topic) as topic_count'), 'topic')
                ->where('topic', '<>', '')
                ->where('topic', 'like', '%' . $filter['topic'] . '%')
                ->groupBy('topic')
                ->orderBy('topic_count', 'desc')
                ->skip(($filter['start'] - 1) * $filter['limit'])
                ->limit($filter['limit'])
                ->get();

            if (!empty($topics)) {
                return $this->responseSuccess($topics, '动态话题列表获取成功');
            } else {
                return $this->responseError('动态话题获取失败');
            }

        });

    }

    /**
     * @SWG\Get(
     *   path="/moment/like/lists",
     *   summary="点赞列表",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="id", in="header", required=true, description="帖子id", type="integer"),
     *   @SWG\Parameter(name="last_id", in="query", required=true, description="最后一条点赞id", type="integer"),
     *   @SWG\Parameter(name="limit", in="query", required=true, description="分页大小", type="integer"),
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
    public function likes()
    {
        $filter['id'] = Request::get('id');
        $filter['last_id'] = Request::get('last_id') ?: 0;
        $filter['limit'] = Request::get('limit') ?: 20;

        //参数校验
        try {
            $filter = $this->dataCheck($filter);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
        $moment = Moment::find($filter['id']);

        if (empty($moment)) {
            return $this->responseError('动态不存在', 404);
        }

        $key = "moment" . $filter['id'] . "-like-lists-" . $filter['last_id'] . '-' . $filter['limit'];

        return cache_remember($key, 1, function () use ($filter, $moment) {
            unset($filter['id']);
            $likes = $moment->likes()
                ->filter($filter)
                ->orderBy('id', 'desc')
                ->limit($filter['limit'])
                ->get();

            $likes = $likes->transform(function ($like) {
                $member = $like->member()->select('id', 'name', 'avatar_url', 'title', 'type', 'is_certified')->first();

                $member->avatar_url = get_file_url($member->avatar_url);
                $member->avatar_thumb = get_file_url($member->avatar_url) . '/thumbnail';
                $member->is_followed = 0;
                //登录用户是否关注点赞用户
                $member2 = null;
                if (\JWTAuth::getToken()) {
                    try {
                        $member2 = \JWTAuth::parseToken()->authenticate();
                    } catch (Exception $e) {

                    }
                }
                if ($member2) {
                    $member->is_followed = empty($member->follows()->where('member_id', $member2->id)->first()) ? 0 : 1;
                }
                $member = $member->getAttributes();
                $like->member = $member;

                return $like;
            });

            return $this->responseSuccess($likes);

        });

    }

    /**
     * @SWG\Get(
     *   path="/moment/delete",
     *   summary="话题列表",
     *   tags={"/moment 社区"},
     *   @SWG\Parameter(name="token", in="header", required=true, description="用户登录token", type="string"),
     *   @SWG\Parameter(name="id", in="query", required=true, description="动态id", type="integer"),
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

    public function delete()
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

        $moment = Moment::find($input['id']);
        if (empty($moment)) {
            return $this->responseError('动态不存在', 404);
        }
        //校验登录用户是否具有删除权限
        if ($moment->member_id !== $member->id) {
            return $this->responseError('您没有权限操作此动态');
        }

        $result = $moment->delete();
        if ($result) {
            return $this->responseSuccess('动态删除成功');
        } else {
            return $this->responseError('动态删除失败，请稍后再试');
        }

    }

}