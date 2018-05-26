<?php

namespace App\Api\Controllers;

use App\Models\OfflineCourse;
use Request;
use Exception;
use Validator;

class OfflineCourseController extends BaseController
{
    public function __construct()
    {
    }

    public function transform($offlinecourse)
    {
        $offlinecourse->price = $offlinecourse->price / 100;
        $attributes = $offlinecourse->getAttributes();

        $attributes['created_at'] = empty($offlinecourse->created_at) ? '' : $offlinecourse->created_at->toDateTimeString();
        $attributes['updated_at'] = empty($offlinecourse->updated_at) ? '' : $offlinecourse->updated_at->toDateTimeString();
        return $attributes;
    }

    public function check($input, $type)
    {
        if ($type == 'add') {
            $rules['city_id'] = 'required|integer';
        }

        $rules = [
            'id' => 'required|integer',
            'title' => 'required|string',
            'charging_type' => 'required|integer',
            'price' => 'required|integer',
            'intro' => 'required|string',
            'content' => 'required|string',
            'feature' => 'required|string',
            'note' => 'required|string',
        ];

        $message = [
            'id.integer' => 'id必须为整数',
            'id.required' => 'id不能为空',
            'title.required' => 'title不能为空',
            'title.integer' => 'title必须为字符串',
            'city_id.integer' => 'city_id必须为整数',
            'city_id.required' => 'city_id不能为空',
            'charging_type.integer' => 'charging_type必须为整数',
            'charging_type.required' => 'charging_type不能为空',
            'price.integer' => 'price必须为整数',
            'price.required' => 'price不能为空',
            'intro.integer' => 'intro必须为字符串',
            'intro.required' => 'intro不能为空',
            'content.integer' => 'content必须为字符串',
            'content.required' => 'content不能为空',
            'feature.integer' => 'feature必须为字符串',
            'feature.required' => 'feature不能为空',
            'note.integer' => 'note必须为字符串',
            'note.required' => 'note不能为空',
        ];

        $validate = Validator::make($input, $rules, $message);

        if ($validate->fails()) {
            throw new Exception($validate->errors()->first());
        }

        return true;
    }

    /**
     * @SWG\Get(
     *   path="/reservation/course/lists",
     *   summary="获取约课课程列表",
     *   tags={"/reservation 约课"},
     *   @SWG\Parameter(name="city_id", in="query", required=true, description="城市id", type="string"),
     *   @SWG\Parameter(name="last_id", in="query", required=true, description="最后一条记录id", type="string"),
     *   @SWG\Parameter(name="limit", in="query", required=true, description="分页大小", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="获取约课老师列表成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function lists()
    {
        $filter['last_id'] = Request::get('last_id') ?: 0;
        $filter['city_id'] = Request::get('city_id') ?: 0;
        $filter['limit'] = Request::get('limit') ?: 20;

        try {
            $check = $this->dataCheck($filter);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "course-list-" . $filter['city_id'] . "-" . $filter['limit'] . "-" . $filter['last_id'];

        return cache_remember($key, 1, function () use ($filter) {
            $courses = OfflineCourse::filter($filter)
                ->select('id', 'title', 'intro', 'bought_num', 'score', 'teacher_id')
                ->limit($filter['limit'])
                ->get();

            $courses->transform(function ($course) {
                $teacher = $course->teacher()->select('id', 'member_id')->first();
                $member = $teacher->member()->first();
                $course->teacher_name = $member->name;
                $course->teacher_avatar = $member->avatar_url;

                return $course;
            });

            if ($courses->count() > 0) {
                return $this->responseSuccess($courses);
            } else {
                return $this->responseError('该地区暂无教师开设线下课程');
            }
        });

    }

    /**
     * @SWG\Get(
     *   path="/reservation/course/add",
     *   summary="添加约课课程",
     *   tags={"/reservation 约课"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="线下课程ID", type="string"),
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
            $type = 'add';
            //参数校验
            $this->check($input, $type);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $teacher = $member->teacher()->first();
        $input['state'] = OfflineCourse::STATE_PUBLISHED;
        $result = $teacher->courses()->create($input);

        if ($result) {
            return $this->responseSuccess($result);
        } else {
            return $this->responseError('约课课程添加失败');
        }
    }

    /**
     * @SWG\Get(
     *   path="/reservation/course/update",
     *   summary="修改约课课程",
     *   tags={"/reservation 约课"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="线下课程ID", type="string"),
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
    public function update()
    {
        $input = Request::input();
        $id = $input['id'];

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 401);
        }

        try {
            $type = 'update';
            //参数校验
            $this->check($input, $type);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        //去掉id
        unset($input['id']);

        $teacher = $member->teacher()->first();
        $course = OfflineCourse::find($id);
        //课程存在校验
        if (empty($course)) {
            return $this->responseError('此课程不存在', 404);
        }

        //判断登录老师是否和课程老师一致？
        if ($course->teacher_id !== $teacher->id) {
            return $this->responseError('您无权操作此课程', 403);
        }

        $result = $course->update($input);
        if ($result) {
            return $this->responseSuccess();
        } else {
            return $this->responseError('约课课程修改失败');
        }
    }

    /**
     * @SWG\Get(
     *   path="/reservation/course/delete",
     *   summary="删除约课课程",
     *   tags={"/reservation 约课"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="线下课程ID", type="string"),
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
        $input['id'] = Request::input('id');

        try {
            $this->dataCheck($input);

            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $teacher = $member->teacher()->first();
        $course = OfflineCourse::find($input['id']);

        if (empty($course)) {
            return $this->responseError('未查到此课程');
        }
        //判断登录老师是否和课程老师一致？
        if ($course->teacher_id !== $teacher->id) {
            return $this->responseError('您无权操作此课程');
        }

        $result = $course->delete();
        if ($result) {
            return $this->responseSuccess();
        } else {
            return $this->responseError('约课课程删除失败');
        }
    }

}