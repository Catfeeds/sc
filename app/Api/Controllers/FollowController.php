<?php

namespace App\Api\Controllers;

use App\Models\Follow;
use App\Models\Module;
use App\Models\Member;
use Exception;
use Request;
use Validator;

class FollowController extends BaseController
{
    public function transform($follow)
    {
        $attributes = $follow->refer->getAttributes();
        $attributes['follow_id'] = $follow->id;
        $attributes['images'] = $follow->refer->images()->transform(function ($item) use ($follow) {
            return [
                'id' => $item->id,
                'title' => !empty($item->title) ?: $follow->refer->title,
                'url' => get_image_url($item->url),
                'summary' => $item->summary,
            ];
        });
        foreach ($follow->refer->getDates() as $date) {
            $attributes[$date] = empty($follow->refer->$date) ? '' : $follow->refer->$date->toDateTimeString();
        }
        return $attributes;
    }

    //参数校验
    public function check($input)
    {
        $rules = [];

        if (isset($input['member_id'])) {
            $rules['member_id'] = 'required|integer';
        }

        $message = [
            'member_id.required' => 'member_id不能为空',
            'member_id.integer' => 'member_id必须为整数',
        ];

        $validate = Validator::make($input, $rules, $message);
        if ($validate->fails()) {
            throw new Exception($validate->errors()->first());
        }

        return true;
    }

    /**
     * @SWG\Get(
     *   path="/follows",
     *   summary="获取我的关注",
     *   tags={"/follows 关注"},
     *   @SWG\Parameter(name="site_id", in="query", required=true, description="站点ID", type="string"),
     *   @SWG\Parameter(name="type", in="query", required=true, description="类型", type="string"),
     *   @SWG\Parameter(name="page_size", in="query", required=true, description="分页大小", type="integer"),
     *   @SWG\Parameter(name="page", in="query", required=true, description="分页序号", type="integer"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="查询成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function lists()
    {

        $input['start'] = Request::get('start') ?: 1;
        $input['limit'] = Request::get('limit') ?: 20;

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录');
            }
        } catch (Exception $e) {
            return $this->responseError('无效的token,请重新登录');
        }

        try {
            $input = $this->dataCheck($input);
            if (!$input) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $star_ids = $member->follows()->select('member_id')->get()->toArray();

        $stars = Member::whereIn('id', $star_ids)
            ->select('id', 'name', 'avatar_url', 'nick_name', 'is_certified', 'title', 'type')
            ->skip(($input['start'] - 1) * $input['limit'])
            ->limit($input['limit'])
            ->get();

        $stars->transform(function ($star) {
            $star->avatar_url = get_file_url($star->avatar_url);
            if ($star->is_certified != Member::IS_CERTIFIED) {
                $star->name = $star->nick_name;
            }
            $star->is_followed = 1;
            $attr = $star->getAttributes();
            $attr['member_id'] = $star->id;
            unset($attr['id']);
            unset($attr['nick_name']);

            return $attr;
        });

        return $this->responseSuccess($stars);
    }

    /**
     * @SWG\Get(
     *   path="/fans/lists",
     *   summary="获取会员粉丝",
     *   tags={"/follows 关注"},
     *   @SWG\Parameter(name="token", in="query", required=true, description="token", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="正常"
     *   ),
     *   @SWG\Response(
     *     response="401",
     *     description="无效"
     *   )
     * )
     */
    public function fans()
    {

        $input['start'] = Request::get('start') ?: 1;
        $input['limit'] = Request::get('limit') ?: 20;

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 401);
        }
        //获取我的关注id
        $follow_ids = $member->follows()->pluck('member_id')->toArray();

        $fans_ids = Follow::where('member_id', $member->id)
            ->select('refer_id')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        $fans = Member::whereIn('id', $fans_ids)
            ->select('id', 'name', 'avatar_url', 'nick_name', 'is_certified', 'title', 'type')
            ->skip(($input['start'] - 1) * $input['limit'])
            ->limit($input['limit'])
            ->get();

        $fans->transform(function ($fan) use ($follow_ids) {
            $fan->avatar_url = get_file_url($fan->avatar_url);
            if ($fan->certified != Member::IS_CERTIFIED) {
                $fan->name = $fan->nick_name;
            }
            //查询是否关注粉丝
            $fan->is_followed = in_array($fan->id, $follow_ids) ? 1 : 0;
            $attr = $fan->getAttributes();
            $attr['member_id'] = $fan->id;
            unset($attr['id']);
            unset($attr['nick_name']);

            return $attr;
        });

        return $this->responseSuccess($fans);
    }

    /**
     * @SWG\Post(
     *   path="/follow/add",
     *   summary="添加关注",
     *   tags={"/follows 关注"},
     *   @SWG\Parameter(name="member_id", in="query", required=true, description="ID", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token", type="string"),
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
        $input['member_id'] = Request::get('member_id');

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录');
            }
        } catch (Exception $e) {
            return $this->responseError('无效的token,请重新登录');
        }

        try {
            $check = $this->check($input);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $mem = Member::find($input['member_id']);
        if (empty($mem)) {
            return $this->responseError('用户不存在', 404);
        }

        $exist = $member->follows()->where('member_id', $input['member_id'])->first();

        if (!empty($exist)) {
            return $this->responseError('已关注，请勿重复关注。');
        }

        //增加关注记录
        $member->follows()->create([
            'member_id' => $input['member_id'],
        ]);

        return $this->responseSuccess();
    }

    /**
     * @SWG\Get(
     *   path="/follows/delete",
     *   summary="取消关注",
     *   tags={"/follows 关注"},
     *   @SWG\Parameter(name="member_id", in="query", required=true, description="ID", type="integer"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="取消成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到"
     *   )
     * )
     */
    public function delete()
    {
        $input['member_id'] = Request::get('member_id');

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录');
            }
        } catch (Exception $e) {
            return $this->responseError('无效的token,请重新登录');
        }

        try {
            $check = $this->check($input);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $mem = Member::find($input['member_id']);
        if (empty($mem)) {
            return $this->responseError('用户不存在', 404);
        }

        $follow = $member->follows()->where('member_id', $input['member_id'])->first();

        if (!$follow) {
            return $this->responseError('未关注此用户');
        }

        $follow->delete();

        return $this->responseSuccess();
    }

    /**
     * @SWG\Get(
     *   path="/follows/exist",
     *   summary="是否关注",
     *   tags={"/follows 关注"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="ID", type="string"),
     *   @SWG\Parameter(name="type", in="query", required=true, description="类型", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="已关注"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="未关注"
     *   )
     * )
     */
    public function exist()
    {
        $id = Request::get('id');
        $type = Request::get('type');

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录');
            }
        } catch (Exception $e) {
            return $this->responseError('无效的token,请重新登录');
        }

        $module = Module::findByName($type);
        if (!$module) {
            return $this->responseError('此类型不存在');
        }

        $follow = Follow::where('refer_id', $id)
            ->where('refer_type', $module->model_class)
            ->where('member_id', $member->id)
            ->first();

        if ($follow) {
            return $this->responseSuccess();
        } else {
            return $this->responseError('未关注');
        }
    }
}