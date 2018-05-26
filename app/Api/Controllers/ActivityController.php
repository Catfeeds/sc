<?php

namespace App\Api\Controllers;

use App\Models\Activity;
use Request;
use Exception;
use Carbon\Carbon;

class ActivityController extends BaseController
{
    public function __construct()
    {
    }

    public function transform($activity)
    {
        $attributes = $activity->getAttributes();
        $now = Carbon::now();
        if ($activity->start_at > $now) {
            $attributes['status'] = Activity::STATUS_COMMING;
        } elseif ($activity->start_at < $now && $activity->end_at > $now) {
            $attributes['status'] = Activity::STATUS_ONGING;
        } elseif ($activity->end_at < $now) {
            $attributes['status'] = Activity::STATUS_END;
        }
        $attributes['cover_url'] = get_file_url($activity->cover_url) . '/thumbnail';

        $attributes['start_at'] = empty($activity->start_at) ? '' : $activity->start_at->toDateTimeString();
        $attributes['end_at'] = empty($activity->end_at) ? '' : $activity->end_at->toDateTimeString();
        return $attributes;
    }

    /**
     * @SWG\Get(
     *   path="/activity/lists",
     *   summary="获取活动列表",
     *   tags={"/activity 活动"},
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

        //参数校验
        try {
            $check = $this->dataCheck($filter);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $key = "activity-list-" . "-" . $filter['last_id'] . "-" . $filter['limit'];
        return cache_remember($key, 0, function () use ($filter) {
            $activities = Activity::filter($filter)
                ->where('state', Activity::STATE_PUBLISHED)
                ->select('id', 'title', 'cover_url', 'web_url', 'start_at', 'end_at')
                ->orderBy('sort', 'desc')
                ->limit($filter['limit'])
                ->get();

            if (!empty($activities)) {
                $activities->transform(function ($activity) {
                    return $this->transform($activity);
                });

                return $this->responseSuccess($activities);
            } else {
                return $this->responseError('没有找到文章', 404);
            }
        });
    }
}