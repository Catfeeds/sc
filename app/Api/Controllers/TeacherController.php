<?php

namespace App\Api\Controllers;

use App\Models\Dictionary;
use App\Models\Teacher;
use Exception;
use Request;
use Validator;

class TeacherController extends BaseController
{
    /**
     * @SWG\Get(
     *   path="/teacher/city/lists",
     *   summary="获取约课老师所在城市列表",
     *   tags={"/reservation 约课"},
     *   @SWG\Parameter(name="start", in="query", required=true, description="页码", type="string"),
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
    public function cities()
    {

        $key = "teacher-city-list";
        $state = Teacher::STATE_PUBLISHED;

        return cache_remember($key, 1, function () use ($state) {
            $cityArr = Teacher::where('state', $state)
                ->groupBy('city_id')
                ->pluck('city_id');

            $cities = Dictionary::where('name', 'city')
                ->whereIn('id', $cityArr)
                ->select('id', 'value')
                ->get();

            if ($cities) {
                return $this->responseSuccess($cities);
            } else {
                return $this->responseError('未找到教师相关城市');
            }
        });

    }

    /**
     * @SWG\Get(
     *   path="/reservation/teacher/lists",
     *   summary="获取约课老师列表",
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
        $filter['limit'] = Request::get('limit') ?: 20;
        $filter['city_id'] = Request::get('city_id') ?: 0;

        $key = "teacher-list-" . $filter['city_id'] . "-" . $filter['limit'] . "-" . $filter['last_id'];

        //参数校验
        try {
            $check = $this->dataCheck($filter);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return cache_remember($key, 1, function () use ($filter) {
            $teachers = Teacher::filter($filter)
                ->select('id', 'member_id')
                ->limit($filter['limit'])
                ->get();

            $teachers->transform(function ($teacher) {
                $member = $teacher->member()->first();
                $teacher->avatar_url = get_file_url($member->avatar_url);
                $teacher->name = $member->name;
                $teacher->course = $teacher->courses()->select('id', 'title', 'intro', 'bought_num')->get();
                $teacher->getAttributes();

                return $teacher;
            });

            if ($teachers) {
                return $this->responseSuccess($teachers);
            } else {
                return $this->responseError('未找到教师相关课程');
            }
        });

    }

    /**
     * @SWG\Get(
     *   path="/reservation/teacher/detail",
     *   summary="获取约课老师详情",
     *   tags={"/reservation 约课"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="教师id", type="string"),
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
    public function detail()
    {
        $input['id'] = Request::get('id');

        //参数校验
        try {
            $check = $this->dataCheck($input);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }

            $teacher = Teacher::find($input['id'], ['id', 'member_id', 'title', 'cover_url', 'teach_year', 'teach_exp', 'self_outcome', 'teach_outcome', 'meet_num']);

            if (empty($teacher)) {
                return $this->responseError('未找到教师', 404);
            }

            $member = $teacher->member()->first();
            $teacher->name = $member->name;
            $teacher->cover_url = get_file_url($teacher->cover_url);
            $teacher->avatar_url = get_file_url($member->avatar_url);

        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        if ($teacher) {
            return $this->responseSuccess($teacher);
        }

    }

    /**
     * @SWG\Get(
     *   path="/reservation/teacher/comments",
     *   summary="获取约课老师评价列表",
     *   tags={"/reservation 约课"},
     *   @SWG\Parameter(name="city_id", in="query", required=true, description="城市id", type="string"),
     *   @SWG\Parameter(name="last_id", in="query", required=true, description="最后一条记录id", type="string"),
     *   @SWG\Parameter(name="limit", in="query", required=true, description="分页大小", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="获取约课老师评价列表成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function comments()
    {
        $filter['teacher_id'] = Request::get('teacher_id');
        $filter['last_id'] = Request::get('last_id') ?: 0;
        $filter['limit'] = Request::get('limit') ?: 20;

        //参数校验
        try {
            $check = $this->dataCheck($filter);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "teacher-comments-" . $filter['teacher_id'] . "-" . $filter['limit'] . "-" . $filter['last_id'];

        return cache_remember($key, 1, function () use ($filter) {
            $teacher = Teacher::find($filter['teacher_id']);

            $comments = $teacher->comments()
                ->filter($filter)
                ->select('id', 'course_id', 'member_id', 'content', 'score', 'like_num', 'created_at')
                ->limit($filter['limit'])
                ->get();

            foreach ($comments as $comment) {
                $comment->course = $comment->course()->first()->title;
                $comment->member = $comment->member()->select('id', 'name', 'avatar_url')->first();
                if (!empty($comment->member)) {
                    $comment->member->avatar_url = get_file_url($comment->member->avatar_url);
                }
            }

            if ($comments) {
                return $this->responseSuccess($comments);
            } else {
                return $this->responseError('未找到教师相关课程');
            }

        });

    }

    /**
     * @SWG\Get(
     *   path="/reservation/teacher/update",
     *   summary="修改约课老师信息",
     *   tags={"/reservation 约课"},
     *   @SWG\Parameter(name="id", in="query", required=true, description="教师id", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="修改约课老师信息成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function update()
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
            $this->check($input);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $teacher = $member->teacher()->first();
        $result = $teacher->update($input);

        if ($result) {
            return $this->responseSuccess($result);
        } else {
            return $this->responseError('未找到教师', 404);
        }

    }

    public function check($input)
    {
        $rules = [
            'title' => 'required|string',
            'city_id' => 'required|integer',
            'teach_year' => 'required|integer',
            'teach_exp' => 'required|string',
            'organization' => 'required|string',
            'self_outcome' => 'required|string',
            'teach_outcome' => 'required|string',
        ];

        $message = [
            'title.required' => 'title不能为空',
            'title.integer' => 'title必须为字符串',
            'city_id.integer' => 'city_id必须为整数',
            'city_id.required' => 'city_id不能为空',
            'teach_year.integer' => 'teach_year必须为整数',
            'teach_year.required' => 'teach_year不能为空',
            'teach_exp.integer' => 'teach_exp必须为字符串',
            'teach_exp.required' => 'teach_exp不能为空',
            'organization.integer' => 'organization必须为字符串',
            'organization.required' => 'organization不能为空',
            'self_outcome.integer' => 'self_outcome必须为字符串',
            'self_outcome.required' => 'self_outcome不能为空',
            'teach_outcome.integer' => 'teach_outcome必须为字符串',
            'teach_outcome.required' => 'teach_outcome不能为空',
        ];

        $validate = Validator::make($input, $rules, $message);

        if ($validate->fails()) {
            throw new Exception($validate->errors()->first());
        }

        return true;
    }

}