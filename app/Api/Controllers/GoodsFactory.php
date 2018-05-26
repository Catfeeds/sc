<?php

namespace App\Api\Controllers;

use App\Models\Course;
use App\Models\Live;
use App\Models\OfflineCourse;

trait GoodsFactory
{
    protected $goodsObj;

    public function getObj($type, $goods_id)
    {
        if ($this->goodsObj) {
            return $this->goodsObj;
        } else {
            if ($type == 'course') {
                $this->goodsObj = Course::find($goods_id);
            } elseif ($type == 'live') {
                $this->goodsObj = Live::find($goods_id);
            } elseif ($type == 'reservation') {
                $this->goodsObj = OfflineCourse::find($goods_id);
            }
            return $this->goodsObj;
        }

        return false;
    }

}