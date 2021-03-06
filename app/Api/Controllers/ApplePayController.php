<?php

namespace App\Api\Controllers;

use App\Models\OfflineCourse;
use App\Models\LiveMember;
use App\Models\Teacher;
use App\Models\Order;
use App\Models\Cart;
use Carbon\Carbon;
use Request;

class ApplePayController extends BaseController
{
    public function createOrder()
    {
        $input = Request::input();

        if ($input['type'] == 'reservation' && empty(Teacher::find($input['teacher_id']))) {
            return $this->responseError('teacher_id不合法');
        }

        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 401);
        }

        // 验证参数
        if (strlen($input['receipt']) < 20) {
            return $this->responseError('非法参数：receipt');
        }
        //请求验证
        $html = $this->verify($input['receipt']);
        $data = json_decode($html, true);

        // 如果是沙盒数据 则验证沙盒模式
        if ($data['status'] == '21007') {
            // 请求验证
            $html = $this->verify($input['receipt'], 1);
            $data = json_decode($html, true);
            $data['sandbox'] = '1';
        }

        return $this->responseSuccess($data);

        $data['status'] = 0;
        // 判断是否购买成功,成功则创建订单
        if (intval($data['status']) === 0) {
            //待判断支付价格和商品价格是否一致 todo

            //根据类型以及商品id获取商品对象
            $goodsObj = $this->getObj($input['type'], $input['goods_id']);

            $arr = [
                'member_id' => $member->id,
                'order_sn' => Order::buildOrderSn(),//生成订单号
                'payment' => 'applePay',
                'amount' => '',
                'buyer_id' => '',
                'pay_sn' => '',
                'state' => Order::STATE_PAID,
                'paid_at' => Carbon::now(),
            ];
            $order = Order::create($arr);

            if ($order) {
                $result = [
                    'member_id' => $member->id,
                    'order_id' => $order->id,
                    'name' => $goodsObj->title,
                    'image' => empty($goodsObj->cover_url) ?: $goodsObj->cover_url,
                    'price' => $goodsObj->price,
                    'num' => Cart::NUM_DEFAULT,
                    'detail' => $goodsObj->intro,
                    'state' => Cart::STATE_PAID,
                ];

                $goods_cart = $goodsObj->carts()->create($result);
                //如果有约课还需要添加约课记录
                if ($goodsObj instanceof OfflineCourse && $goods_cart) {
                    $result['charging_type'] = $goodsObj->charging_type;
                    $result['start_at'] = $input['start_at'];
                    $result['teacher_id'] = $input['teacher_id'];
                    $goodsObj->records()->create($result);
                }
                //创建各种关系
                $goodsObj->increment('bought_num');
                if ($input['type'] == 'live') {
                    $goodsObj->members()->attach($member->id, ['type' => LiveMember::TYPE_NORMAL]);
                } elseif ($input['type'] == 'course') {
                    $goodsObj->members()->attach($member->id);
                }

            }

            return $this->responseSuccess();

        } else {
            return $this->responseError('购买失败', $data['status']);
        }
    }

    public function verify($receipt, $sandbox = 0)
    {
        //小票信息
        $POSTFIELDS = array("receipt-data" => $receipt);
        $POSTFIELDS = json_encode($POSTFIELDS);

        //正式购买地址 沙盒购买地址
        $url_buy = "https://buy.itunes.apple.com/verifyReceipt";
        $url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
        $url = $sandbox ? $url_sandbox : $url_buy;

        //简单的curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}