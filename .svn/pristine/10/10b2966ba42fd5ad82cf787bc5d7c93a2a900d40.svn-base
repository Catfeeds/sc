<?php

namespace App\Models;

use Auth;
use Request;
use Response;
use Carbon\Carbon;

class Coupon extends BaseModule
{
    const MODEL_CLASS = 'App\Models\Coupon';

    const STATE_UNUSED = 0;
    const STATE_USED = 1;
    const STATE_INVALID = 2;

    const REFER_TYPES = [
        'App\Models\Course' => '课程',
        'App\Models\Live' => '直播',
        'App\Models\OfflineCourse' => '线下课程',
    ];

    const TYPES = [
        0 => '打折',
        1 => '抵扣',
    ];

    const STATES = [
        0 => '未使用',
        1 => '已使用',
        2 => '失效',
    ];

    protected $fillable = [
        'refer_id',
        'refer_type',
        'code',
        'type',
        'discount',
        'batch_id',
        'order_id',
        'member_id',
        'state',
        'use_at',
        'deadline',
        'received_at',
    ];

    protected $dates = ['use_at', 'deadline', 'received_at', 'created_at', 'updated_at'];

    public function scopeFilter($query, $filters)
    {
        $query->where(function ($query) use ($filters) {
            empty($filters['id']) ?: $query->where('id', $filters['id']);
            empty($filters['start_date']) ?: $query->where('deadline', '>=', $filters['start_date']);
            empty($filters['end_date']) ?: $query->where('deadline', '<=', $filters['end_date']);
            empty($filters['member_name']) ?: $query->whereHas('member', function ($query) use ($filters) {
                $query->where('nick_name', $filters['member_name']);
            });
            empty($filters['member_id']) ?: $query->whereHas('member', function ($query) use ($filters) {
                $query->where('id', $filters['member_id']);
            });
        });

        if (isset($filters['state'])) {
            if (!empty($filters['state']) || $filters['state'] === strval(static::STATE_DELETED)) {
                $query->where('state', $filters['state']);
            }
        }
    }

    public static function stores($input)
    {
        $input['batch_id'] = 1;
        $num = $input['num'];
        unset($input['num']);

        $coupon = static::where('batch_id', '<>', 0)->orderBy('id', 'desc')->first();
        if ($coupon) {
            $input['batch_id'] = $coupon->batch_id + 1;
        }

        $data = [];
        for ($i = 0; $i < $num; $i++) {
            foreach ($input as $key => $val) {
                $data[$i][$key] = $val;
            }
            //生成优惠码
            $data[$i]['code'] = str_rand(12);

            $data[$i]['user_id'] = Auth::user()->id;
            $data[$i]['state'] = static::STATE_UNUSED;
            $data[$i]['created_at'] = Carbon::now();
        }
        $coupons = new static;

        return $coupons::insert($data);
    }

    public static function table()
    {
        $filters = Request::all();

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;

        $ds = new DataSource();
        $coupons = static::with('member', 'user')
            ->filter($filters)
            ->skip($offset)
            ->limit($limit)
            ->get();

        $ds->total = static::filter($filters)
            ->count();

        $coupons->transform(function ($coupon) {

            $coupon->member_name = $coupon->user_name = '';
            if ($coupon->member) {
                $coupon->member_name = $coupon->member->nick_name;
            }
            if ($coupon->user) {
                $coupon->user_name = $coupon->user->name;
            }

            $attributes = $coupon->getAttributes();

            //日期类型
            foreach ($coupon->dates as $date) {
                $attributes[$date] = empty($coupon->$date) ? '' : $coupon->$date->toDateTimeString();
            }

            $attributes['state_name'] = $coupon->stateName();
            return $attributes;
        });

        $ds->rows = $coupons;

        return Response::json($ds);
    }
}