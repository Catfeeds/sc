<?php

namespace App\Api\Controllers;

use App\Libraries\wyyIm\IstantMessage;
use App\Libraries\aliSms\SmsAli;
use App\Models\Course;
use App\Models\Follow;
use App\Models\Gallery;
use App\Models\Member;
use App\Models\Image;
use App\Models\Item;
use Exception;
use Validator;
use Request;
use Cache;
use App\Models\Live;

class MemberController extends BaseController
{
    protected $module;

    public function __construct()
    {
        $this->module = $this->getModule(__CLASS__);
    }

    public function transform($member)
    {
        $member->avatar_url = get_file_url($member->avatar_url);

        if ($member->is_certified != Member::IS_CERTIFIED) {
            $member->name = $member->nick_name;
        }
        $member->member_id = $member->id;

        $attributes = $member->getAttributes();
        unset($attributes['id']);
        unset($attributes['password']);
        unset($attributes['token']);

        $attributes['created_at'] = empty($member->created_at) ? '' : $member->created_at->toDateTimeString();
        $attributes['updated_at'] = empty($member->updated_at) ? '' : $member->updated_at->toDateTimeString();

        return $attributes;
    }

    public function check($input)
    {
        $rules = [
            'avatar_url' => 'string',
            'nick_name' => 'string',
            'sex' => 'digits:1',
            'province' => 'numeric',
            'city' => 'numeric',
            'district' => 'numeric',
            'studio' => 'string',
            'type' => 'numeric',
            'school' => 'string',
        ];

        if (isset($input['mobile'])) {
            $rules['mobile'] = 'required|unique:members';
        }
        if (isset($input['password'])) {
            $rules['password'] = 'required|min:6';
        }
        if (isset($input['captcha'])) {
            $rules['captcha'] = 'required|digits:6';
        }

        $message = [
            'avatar_url.string' => '头像必须为字符串',
            'nick_name.string' => '昵称必须为字符串',
            'sex.digits' => '性别必须为数字',
            'province.numeric' => '省必须为数字',
            'city.numeric' => '市必须为数字',
            'district.numeric' => '区（县）必须为数字',
            'studio.string' => '画室必须为字符串',
            'type.numeric' => '身份必须为数字',
            'school.string' => '学校必须为字符串',
            'captcha.required' => '验证码不能为空',
            'captcha.digits' => '验证码必须为6位数字',
            'mobile.required' => '手机号不能为空',
            'mobile.unique' => ':attribute已注册',
            'password.required' => '密码不能为空',
            'password.string' => '密码必须为字符串',
            'password.min' => '密码必须不少于6位',
        ];

        $validate = Validator::make($input, $rules, $message, ['mobile' => '手机号',]);

        if ($validate->fails()) {
            throw new Exception($validate->errors()->first());
        }

        return true;
    }

    /**
     * @SWG\Get(
     *   path="/members/login",
     *   summary="会员登录",
     *   tags={"/members 会员"},
     *   @SWG\Parameter(name="member_name", in="query", required=true, description="会员名", type="string"),
     *   @SWG\Parameter(name="password", in="query", required=true, description="密码", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="登录成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function login()
    {
        $mobile = Request::get('mobile');
        $password = empty(Request::get('password')) ? '' : Request::get('password');
        $captcha = empty(Request::get('captcha')) ? '' : Request::get('captcha');

        if (!preg_match("/1[34578]{1}\d{9}$/", $mobile)) {
            return $this->responseError('请输入正确的手机号', 401);
        }

        if (empty($captcha) && empty($password)) {
            return $this->responseError('请输入密码或验证码');
        }

        try {
            $member = Member::where('mobile', $mobile)
                ->first();

            if (empty($member)) {
                return $this->responseError('此用户未注册', 404);
            }

            if (!empty($member) && $member->state == Member::STATE_DISABLED) {
                return $this->responseError('此用户已被禁用', 403);
            }

            if (md5(md5($password) . $member->salt) !== $member->password && !empty($password)) {
                return $this->responseError('账号或密码错误');
            }

            $key = 'captcha_' . Member::CAPTCHA_LOGIN . '_' . $mobile;
            if (!empty($captcha) && Cache::get($key) != $captcha) {
                return $this->responseError('手机验证码错误');
            }

            //旧token作废
//            try {
//                \JWTAuth::refresh($member->token);
//            } catch (Exception $e) {
//                return $this->responseError($e->getMessage());
//            }

            $member->token = \JWTAuth::fromUser($member);

            $member->ip = get_client_ip();

            $member->save();

            //移除验证码
            if (!empty($captcha)) {
                Cache::forget($key);
            }

            $token_data = tokenTool($member->token);

            $member->avatar_thumb = get_file_url($member->avatar_url) . '/thumbnail';
            $member->avatar_url = get_file_url($member->avatar_url);
            $member->expired_time = $token_data['expired_time'];
            $member->refresh_token = $token_data['refresh_token'];
            //查询用户地区
            $detail = $member->detail()->first();

            $member->province = '';
            $member->city = '';
            $member->district = '';
            $member->school = '';
            $member->studio = '';

            if ($detail) {
                $member->province = !empty($detail->province) ? $detail->province : '';
                $member->city = !empty($detail->city) ? $detail->city : '';
                $member->district = !empty($detail->district) ? $detail->district : '';
                $member->school = $detail->school;
                $member->studio = $detail->studio;
            }

            return $this->responseSuccess($member);

        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    /**
     * @SWG\Get(
     *   path="/members/register",
     *   summary="会员注册",
     *   tags={"/members 会员"},
     *   @SWG\Parameter(name="member_name", in="query", required=true, description="会员名", type="string"),
     *   @SWG\Parameter(name="password", in="query", required=true, description="密码", type="string"),
     *   @SWG\Parameter(name="captcha", in="query", required=true, description="验证码", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="注册成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function register()
    {
        $input['mobile'] = Request::get('mobile');
        $input['password'] = Request::get('password');
        $input['captcha'] = Request::get('captcha');

        $mobile = $input['mobile'];
        $password = $input['password'];
        $captcha = $input['captcha'];

        if (!preg_match("/1[34578]{1}\d{9}$/", $mobile)) {
            return $this->responseError('请输入正确的手机号');
        }

        try {
            //参数校验
            $this->check($input);

        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        try {
            //比较验证码
            $key = 'captcha_' . Member::CAPTCHA_REGISTER . '_' . $mobile;
            if (Cache::get($key) != $captcha) {
                return $this->responseError('手机验证码错误');
            }

            $member = Member::where('mobile', $mobile)
                ->first();

            if ($member) {
                return $this->responseError('手机号已注册');
            }

            $salt = str_rand();

            //用手机号注册网易云im账号
            $im = new IstantMessage();
            $Im_data = $im->curl_im(config('site.wyyIm.CreateImUser'), ['accid' => $mobile]);

            if ($Im_data['code'] !== 200) {
                return $this->responseError('注册失败，请稍后再试。');
            }

            $member = Member::create([
                'name' => $mobile,
                'password' => md5(md5($password) . $salt),
                'nick_name' => $mobile,
                'mobile' => $mobile,
                'avatar_url' => url('/images/avatar_member.png'),
                'salt' => $salt,
                'state' => Member::STATE_ENABLED,
                'points' => 0,
                'ip' => get_client_ip(),
            ]);

            //注册用户详情
            $member->detail()->create([
                'province' => 0,
                'city' => 0,
                'district' => 0,
                'school' => '',
                'studio' => '',
            ]);

            $member->token = \JWTAuth::fromUser($member);
            $member->im_token = $Im_data['info']['token'];
            $member->save();

            $member->accid = $Im_data['info']['accid'];

            //移除验证码
            Cache::forget($key);

            return $this->responseSuccess($member);

        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    /**
     * @SWG\Get(
     *   path="/members/homepage",
     *   summary="个人主页",
     *   tags={"/member 会员"},
     *   @SWG\Parameter(name="member_id", in="query", required=true, description="会员id", type="integer"),
     *   @SWG\Response(
     *     response=200,
     *     description="登录成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function homepage()
    {
        $input['id'] = Request::get('member_id') ?: 0;

        if (empty(\JWTAuth::getToken()) && empty($input['id'])) {
            return $this->responseError('请登录或者传入正确的数据', 412);
        }

        try {
            $this->dataCheck($input);

        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        //是否传member_id，不传是查看自己个人主页这种情况
        if (!empty($input['id'])) {
            //查看的用户 个人主页
            $member = Member::find($input['id']);
            //未登录，则未关注
            $member->is_followed = 0;
            //登录则需要判断
            if (\JWTAuth::getToken()) {
                try {
                    $member2 = \JWTAuth::parseToken()->authenticate();
                    if (!$member2) {
                        return $this->responseError('无效的token,请重新登录', 401);
                    }
                } catch (Exception $e) {
                    return $this->responseError($e->getMessage(), 401);
                }

                $member->is_followed = empty($member2->follows()->where('member_id', $member->id)->first()) ? 0 : 1;
                $member->is_self = ($member->id == $member2->id) ? 1 : 0;
            }


            if ($member->is_certified != Member::IS_CERTIFIED) {
                $member->name = $member->nick_name;
            }
            $detail = $member->detail()->first();
            if ($detail) {
                $member->province = $detail->province;
                $member->city = $detail->city;
                $member->district = $detail->district;
            } else {
                $member->province = '';
                $member->city = '';
                $member->district = '';
            }

            //获取关注数和粉丝数
            $member->follow_num = $member->follows()->count();
            $member->fans_num = Follow::where('member_id', $member->id)->count();

        } else {
            if (\JWTAuth::getToken()) {
                try {
                    $member = \JWTAuth::parseToken()->authenticate();
                    if (!$member) {
                        return $this->responseError('无效的token,请重新登录', 401);
                    }
                } catch (Exception $e) {
                    return $this->responseError($e->getMessage(), 401);
                }

                $member->is_self = 1;
                //获取关注数和粉丝数
                $member->follow_num = $member->follows()->count();
                $member->fans_num = Follow::where('member_id', $member->id)->count();
            }
        }

        $member = $this->transform($member);

        return $this->responseSuccess($member);
    }


    /**
     * @SWG\Get(
     *   path="/member/course/lists",
     *   summary="我的课程",
     *   tags={"/member 会员"},
     *   @SWG\Parameter(name="token", in="query", required=true, description="会员id", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="获取成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function courses()
    {
        $filter['last_id'] = Request::get('last_id') ?: 0;
        $filter['limit'] = Request::get('limit') ?: 20;
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

        $courses = $member->courses()
            ->select('id', 'title', 'price', 'bought_num', 'lesson_num', 'type', 'cover_url')
            ->filter($filter)
            ->orderBy('id', 'desc')
            ->limit($filter['limit'])
            ->get();

        $courses->transform(function ($course) {
            $course->cover_url = get_file_url($course->cover_url);
            $course->price /= 100;

            return $course;
        });

        return $this->responseSuccess($courses);
    }

    /**
     * @SWG\Get(
     *   path="/member/detail",
     *   summary="获取会员信息",
     *   tags={"/members 会员"},
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
    public function detail()
    {
        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 401);
        }
        $detail = $member->detail()->select('member_id', 'province', 'city', 'district', 'school', 'studio')->first();

        if ($detail) {
            $member->province = $detail->province;
            $member->city = $detail->city;
            $member->district = $detail->district;
            $member->school = $detail->school;
            $member->studio = $detail->studio;
        }

        $member = $this->transform($member);

        return $this->responseSuccess($member);

    }

    /**
     * @SWG\Get(
     *   path="/member/update",
     *   summary="获取会员信息",
     *   tags={"/members 会员"},
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
    public function update()
    {
        $input = Request::input();

        try {
            $member = \JWTAuth::parseToken()->authenticate();

            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError('无效的token,请重新登录', 401);
        }
        // log
        $this->enterClassLog($this->module, $member->mobile, __CLASS__, __FUNCTION__);

        try {
            $this->check($input);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        //用户修改头像、昵称、性别；
        $result = $member->update($input);

        $data = [];

        //编辑detail字段
        if (isset($input['province'])) {
            $data['province'] = $input['province'];
        }
        if (isset($input['city'])) {
            $data['city'] = $input['city'];
        }
        if (isset($input['district'])) {
            $data['district'] = $input['district'];
        }
        if (isset($input['studio'])) {
            $data['studio'] = $input['studio'];
        }
        if (isset($input['school'])) {
            $data['school'] = $input['school'];
        }

        if (!empty($data)) {
            $detail = $member->detail()->first();
            if ($detail) {
                $detail->update($data);
            } else {
                $detail = $member->detail()->create([
                    'province' => !empty($data['province']) ? $data['province'] : 0,
                    'city' => !empty($data['city']) ? $data['city'] : 0,
                    'district' => !empty($data['district']) ? $data['district'] : 0,
                    'school' => !empty($data['school']) ? $data['school'] : '',
                    'studio' => !empty($data['studio']) ? $data['studio'] : '',
                ]);
            }

            $member->detail = $detail;
        }

        $this->log($this->module, '用户:' . $member->mobile . ' 修改其详细信息.', $input);

        $member = $this->transform($member);

        if ($result) {
            return $this->responseSuccess($member);
        }

    }

    /**
     * @SWG\Get(
     *   path="/member/order/lists",
     *   summary="获取我的订单",
     *   tags={"/members 会员"},
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
    public function orders()
    {
        $filter['last_id'] = Request::get('last_id') ?: 0;
        $filter['limit'] = Request::get('limit') ?: 20;

        //参数校验
        try {
            $filter = $this->dataCheck($filter);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        try {
            $member = \JWTAuth::parseToken()->authenticate();

            if (!$member) {
                return $this->responseError('无效的token,请重新登录');
            }
        } catch (Exception $e) {
            return $this->responseError('无效的token,请重新登录');
        }

        $orders = $member->orders()
            ->select('id', 'order_sn', 'amount', 'coupon_discount', 'created_at')
            ->filter($filter)
            ->orderBy('id', 'desc')
            ->limit($filter['limit'])
            ->get();

        $orders->transform(function ($order) {
            $cart = $order->carts()->select('refer_id', 'refer_type', 'name', 'price', 'image')->first();
            if ($cart->refer_type == 'App\Models\Live') {
                $live = Live::find($cart->refer_id);
                $cart->state = empty($live) ? '' : $live->state;
            }

            $order->amount /= 100;
            $cart->price /= 100;
            $cart->image = get_file_url($cart->image);
            $order->order_id = $order->id;
            $cart = $cart->getAttributes();
            $order = $order->getAttributes();
            unset($cart['refer_type']);
            unset($cart['refer_id']);
            unset($order['id']);
            $order = array_merge($order, $cart);
            return $order;
        });

        return $this->responseSuccess($orders);
    }

    /**
     * @SWG\Get(
     *   path="/member/collection/lists",
     *   summary="获取我的收藏",
     *   tags={"/members 会员"},
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
    public function collections()
    {
        $filter['start'] = Request::get('start') ?: 1;
        $filter['limit'] = Request::get('limit') ?: 20;
        $filter['type'] = Request::get('type');
        //参数校验
        try {
            $filter = $this->dataCheck($filter);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        try {
            $member = \JWTAuth::parseToken()->authenticate();

            if (!$member) {
                return $this->responseError('无效的token,请重新登录');
            }
        } catch (Exception $e) {
            return $this->responseError('无效的token,请重新登录');
        }

        $type = $filter['type'] == 0 ? Course::MODEL_CLASS : Gallery::MODEL_CLASS;

        $favorite_ids = $member->favorites()
            ->filter($filter)
            ->select('refer_id')
            ->where('refer_type', $type)
            ->orderBy('id', 'desc')
            ->skip(($filter['start'] - 1) * $filter['limit'])
            ->limit($filter['limit'])
            ->get()
            ->toArray();

        if ($filter['type'] == 0) {
            //获取收藏课程列表
            $courses = Course::whereIn('id', $favorite_ids)
                ->select('id', 'title', 'subtitle', 'price', 'bought_num', 'lesson_num', 'type', 'cover_url')
                ->get();

            $data = $courses->transform(function ($course) {
                $course->cover_url = get_file_url($course->cover_url);
                $course->price /= 100;

                return $course;
            });
        } else {
            $galleries = Gallery::whereIn('id', $favorite_ids)
                ->select('id', 'name', 'subname', 'cover', 'collect_num', 'pic_num')
                ->get();

            $data = $galleries->transform(function ($gallery) {
                //头像
                $gallery->cover = get_file_url($gallery->cover);
                //三张封面图
                $images = $gallery->images()->where('is_cover', Image::TYPE_COVER)->select('id')->get();
                foreach ($images as $image) {
                    $image->url = get_file_url($image->items()->where('type', Item::TYPE_IMAGE)->pluck('url')->first());
                }
                $gallery->images = $images;

                return $gallery;
            });
        }

        return $this->responseSuccess($data);

    }

    /**
     * @SWG\Get(
     *   path="/member/mobile/captcha",
     *   summary="获取验证码",
     *   tags={"/members 会员"},
     *   @SWG\Parameter(name="mobile", in="query", required=true, description="手机号", type="string"),
     *   @SWG\Parameter(name="type", in="query", required=true, description="类型(0:找回密码,1:注册,2:重置密码,3:绑定手机,4:解除绑定手机)", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="正常"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function getCaptcha()
    {
        $mobile = Request::get('mobile');
        $type = Request::get('type');

        try {
            if (!preg_match("/1[34578]{1}\d{9}$/", $mobile)) {
                return $this->responseError('请输入正确的手机号');
            }

            //判断此手机号24小时内发送短信是否过多-------其实阿里自己有限制
            $times = Cache::get('captcha_times_' . $mobile);
            if (!isset($times)) {
                Cache::add('captcha_times_' . $mobile, 1, 24 * 60);
            } elseif ($times >= 20) {
                return $this->responseError('您今天发送短信次数过多');
            } else {
                Cache::increment('captcha_times_' . $mobile, 1);
            }

            $signName = config("site.smsAli.sign");
            $templateCode = config("site.smsAli.template." . $type);
            $code = random(6);

            $response = SmsAli::sendSms(
                $signName,
                $templateCode,
                $mobile, // 短信接收者
                Array(  // 短信模板中字段的值
                    "code" => $code
                )
            );

            if ($response->Code !== 'OK') {
                return $this->responseError('短信验证码发送失败');
            }

            $key = 'captcha_' . $type . '_' . $mobile;

            if (isset($key)) {
                Cache::forget($key);
            }

            Cache::add('captcha_' . $type . '_' . $mobile, $code, 5);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess();
    }

    /**
     * @SWG\Get(
     *   path="/token/refresh",
     *   summary="刷新token",
     *   tags={"/members 会员"},
     *   @SWG\Parameter(name="member_ie", in="query", required=true, description="token", type="string"),
     *   @SWG\Parameter(name="refresh_token", in="query", required=true, description="旧密码", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="获取成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function refresh()
    {
        $input = Request::input();
        $member = Member::find($input['member_id']);

        $token_data = tokenTool($member->token);
        $refresh_token = $token_data['refresh_token'];

        if ($refresh_token !== $input['refresh_token']) {
            return $this->responseError('刷新token错误，无法刷新token');
        }
        //旧token作废
        try {
            \JWTAuth::refresh($member->token);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }

        $token = \JWTAuth::fromUser($member);
        $member->token = $token;
        $member->save();

        $data['token'] = $token;
        $new_token = tokenTool($member->token);
        $data['expired_time'] = $new_token['expired_time'];
        $data['refresh_token'] = $new_token['refresh_token'];

        return $this->responseSuccess($data);
    }

    /**
     * @SWG\Get(
     *   path="/members/password/reset",
     *   summary="重置密码",
     *   tags={"/members 会员"},
     *   @SWG\Parameter(name="member_name", in="query", required=true, description="会员名", type="string"),
     *   @SWG\Parameter(name="captcha", in="query", required=true, description="验证码", type="string"),
     *   @SWG\Parameter(name="password", in="query", required=true, description="新密码", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="修改成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function resetPassword()
    {
        $mobile = Request::get('mobile');
        $captcha = Request::get('captcha');
        $password = Request::get('password');

        try {
            if (!preg_match("/1[34578]{1}\d{9}$/", $mobile)) {
                throw new Exception('请输入正确的手机号');
            }

            $input = [
                'captcha' => $captcha,
                'password' => $password,
            ];

            $check = $this->check($input);
            if (!$check) {
                return $this->responseError('参数错误，请检查参数');
            }

            //比较验证码
            $key = 'captcha_' . Member::CAPTCHA_RESET . '_' . $mobile;
            if (Cache::get($key) != $captcha) {
                throw new Exception('手机验证码错误');
            }

            $member = Member::where('mobile', $mobile)->first();

            //检查用户是否存在
            if (!$member) {
                return $this->responseError('此用户不存在', 404);
            }

            if ($member->state == Member::STATE_DISABLED) {
                return $this->responseError('此用户已被禁用', 403);
            }
            $member->password = md5(md5($password) . $member->salt);
            $member->save();
            //记录场景数据
            $this->enterClassLog($this->module, $member->mobile, __CLASS__, __FUNCTION__);
            //移除验证码
            Cache::forget($key);

            return $this->responseSuccess();
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }
}