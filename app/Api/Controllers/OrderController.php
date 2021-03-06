<?php

namespace App\Api\Controllers;

use App\Libraries\payLib\AlipayUtil;
use App\Libraries\payLib\PaymentFactory;
use App\Models\LiveMember;
use App\Models\OfflineCourse;
use App\Models\OrderLog;
use App\Models\Record;
use App\Models\Teacher;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Live;
use App\Models\Course;
use Carbon\Carbon;
use Exception;
use Request;
use Validator;
use DB;

class OrderController extends BaseController
{

    public function check($input)
    {
        if ($input['type'] == 'reservation') {
            $rules = [
                'start_at' => 'required|string',
                'teacher_id' => 'required|integer',
            ];
            $message = [
                'teacher_id.integer' => 'teacher_id必须为整数',
                'teacher_id.required' => 'teacher_id不能为空',
                'start_at.integer' => 'start_at必须为字符串',
                'start_at.required' => 'start_at不能为空',
            ];
        }

        $rules['goods_id'] = 'required|integer';
        $message['goods_id.required'] = 'goods_id不能为空';
        $message['goods_id.integer'] = 'goods_id必须为整数';

        $validate = Validator::make($input, $rules, $message);

        if ($validate->fails()) {
            throw new Exception($validate->errors()->first());
        }

        return true;
    }

    /**
     * @SWG\Get(
     *   path="/order/add",
     *   summary="预约",
     *   tags={"/order 订单"},
     *   @SWG\Parameter(name="last_id", in="query", required=true, description="最后一条记录id", type="string"),
     *   @SWG\Parameter(name="limit", in="query", required=true, description="分页大小", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="获取我的订单记录成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function create()
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

            if (!in_array($input['type'], Order::TYPES)) {
                return $this->responseError('请传送正确的产品类型');
            }
            if (!in_array($input['payment'], Order::PAYMENTS)) {
                return $this->responseError('请传送正确的产品类型');
            }
            if ($input['type'] == 'reservation' && empty(Teacher::find($input['teacher_id']))) {
                return $this->responseError('teacher_id不合法');
            }

            //根据类型以及商品id获取商品对象
            $goodsObj = $this->getObj($input['type'], $input['goods_id']);

            if (empty($goodsObj)) {
                return $this->responseError('商品类型或商品id错误，请检查');
            }
            //不同商品对象实现不同precheck
            $result = $goodsObj->precheck($member);

            if (!$result) {
                return $this->responseError('此用户已被禁用或已购买');
            }

            $result['payment'] = $input['payment'];
            $result['type'] = $input['type'];

            //存在未支付订单
            if ($result['exist']) {
                $return = $this->unifiedOrder($result, $result['exist']);
                return $this->responseSuccess($return);
            }

        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        //生成订单号
        $result['order_sn'] = Order::buildOrderSn();

        //创建订单
        $order = Order::create($result);

        //记录订单日志
        OrderLog::record($result['order_sn'], OrderLog::ACTION_CREATE, $order);

        $result['order_id'] = $order->id;

        if ($order) {
            $goods_cart = $goodsObj->carts()->create($result);
            //如果有约课还需要添加约课记录
            if ($goodsObj instanceof OfflineCourse && $goods_cart) {
                $result['charging_type'] = $goodsObj->charging_type;
                $result['start_at'] = $input['start_at'];
                $result['teacher_id'] = $input['teacher_id'];
                $goods_cart = $goodsObj->records()->create($result);
            }
        } else {
            return $this->responseError('订单创建失败');
        }

        if ($order && $goods_cart) {
            $return = $this->unifiedOrder($result, $order);
            return $this->responseSuccess($return);
        }

    }

    public function unifiedOrder($result, $order)
    {
        $payment = PaymentFactory::create($result['payment'], $order);

        //统一下单，返回支付参数
        $data['notifyUrl'] = $payment instanceof AlipayUtil ? Request::getSchemeAndHttpHost() . '/api/alipay/notify' : Request::getSchemeAndHttpHost() . '/api/wxpay/notify';
        $data['outTradeNo'] = $order->order_sn;
        $data['subject'] = $result['type'] . '_pay';
        $data['amount'] = $result['price'] * $result['num'];

        return $payment->unifiedOrder($data);
    }

    public function aliNotify()
    {
        $input = Request::input();
        //log 记录一下
        \Log::info('支付宝回调参数记录', $input);

        if ($input['trade_status'] == 'TRADE_SUCCESS') {
            //验签
            $aliPay = new AlipayUtil();

            $result = $aliPay->rsaCheck($input);
            if ($result) {

                $order = Order::where('order_sn', $input['out_trade_no'])->first();

                $cart = Cart::where('order_id', $order->id)->first();
                //判断订单是否存在，支付价格是否正确
                if (empty($order)) {
                    OrderLog::record($input['out_trade_no'], OrderLog::EXCEPTION, $order, '支付宝支付：未查找到此订单');

                    echo 'failure';
                } elseif ($input['buyer_pay_amount'] != $order->amount / 100) {
                    OrderLog::record($input['out_trade_no'], OrderLog::EXCEPTION, $order, '支付宝支付：用户支付金额与订单不一致');

                    echo 'failure';
                } elseif ($input['app_id'] != config('site.alipay.appId')) {
                    OrderLog::record($input['out_trade_no'], OrderLog::EXCEPTION, $order, '支付宝支付：app_id与美课商户不一致');

                    echo 'failure';
                }
                //判断状态，处理订单
                if ($order->state != Order::STATE_NOPAY || $cart->state != Cart::STATE_NOPAY) {
                    OrderLog::record($input['out_trade_no'], OrderLog::EXCEPTION, $order, '支付宝支付：订单状态异常');

                    echo 'failure';
                }
                $input['state'] = Order::STATE_PAID;
                $input['pay_sn'] = $input['trade_no'];
                $input['buyer_id'] = $input['buyer_logon_id'];
                $input['paid_at'] = Carbon::now();

                //开始事务
                DB::beginTransaction();
                $order->update($input);

                //支付宝支付完成记录订单日志
                OrderLog::record($input['out_trade_no'], OrderLog::ACTION_PAID, $order);

                //处理购物车
                $data['state'] = Cart::STATE_PAID;
                $cart->update($data);

                //分为三种商品（直播、课程、约课），约课要额外处理回调
                if ($cart->refer_type == 'App\Models\Live') {
                    //更新直播购买人数
                    $live = Live::find($cart->refer_id);
                    $result1 = $live->increment('bought_num');
                    //更新直播、用户关联关系
                    $result2 = $live->members()->attach($cart->member_id, ['type' => LiveMember::TYPE_NORMAL]);
                    $msg = ($result1 && $result2) ? 'success' : 'failure';
                } elseif ($cart->refer_type == 'App\Models\OfflineCourse') {
                    //更新线下课程购买人数
                    $offline = OfflineCourse::find($cart->refer_id);
                    $result1 = $offline->increment('bought_num');
                    //更新约课记录状态
                    $record = $offline->records()->where('member_id', $cart->member_id)->first();
                    $record->state = Record::STATE_PAID;
                    $result2 = $record->save();

                    $msg = ($result1 && $result2) ? 'success' : 'failure';
                } else {
                    //更新课程购买人数
                    $course = Course::find($cart->refer_id);
                    $result1 = $course->increment('bought_num');
                    //更新课程、用户关联关系
                    $result2 = $course->members()->attach($cart->member_id);

                    $msg = ($result1 && $result2) ? 'success' : 'failure';
                }

                if ($msg == 'success') {
                    DB::commit();
                } else {
                    DB::rollBack();
                }

                echo $msg;
            }
        } else {
            echo 'failure';
        }
    }

    public function wxNotify()
    {

    }
}