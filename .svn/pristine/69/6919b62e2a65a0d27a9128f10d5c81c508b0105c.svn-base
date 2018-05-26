<?php

namespace App\Models;

use Exception;
use Request;
use Response;


class Cart extends BaseModule
{
    const NUM_DEFAULT = 1;

    const STATE_NOPAY = 0;
    const STATE_PAID = 1;

    const EXIST = 1;

    const STATES = [
        0 => '未支付',
        1 => '已支付',
    ];

    const STATE_PERMISSIONS = [
        0 => '@order-delete',
    ];

    protected $fillable = ['order_id', 'member_id', 'name', 'image', 'price', 'num', 'detail', 'state'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}