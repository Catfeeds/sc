<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\Category;
use App\Models\UserLog;
use App\Models\Coupon;
use Request;
use Response;

/**
 * 优惠券
 */
class CouponController extends Controller
{
    protected $base_url = '/admin/coupons';
    protected $view_path = 'admin.coupons';

    public function __construct()
    {
    }

    public function index()
    {
        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        return view('admin.coupons.create', ['base_url' => $this->base_url]);
    }

    public function store()
    {
        $input = Request::input();

        unset($input['_token']);
        unset($input['category_id']);

        $result = Coupon::stores($input);

        if ($result) {
            $coupons = Coupon::where('type', $input['type'])
                ->where('deadline', $input['deadline'])
                ->get();
            foreach ($coupons as $coupon) {
                event(new UserLogEvent(UserLog::ACTION_CREATE . '优惠券', $coupon->id, Coupon::MODEL_CLASS));
            }

            \Session::flash('flash_success', '批量生成成功');
            return redirect($this->base_url);
        } else {
            \Session::flash('flash_warning', '批量生成失败');
        }

    }

    public function save($id)
    {
        $live = Coupon::find($id);

        if (empty($live)) {
            return;
        }

        $live->update(Request::all());
    }

    public function table()
    {
        return Coupon::table();
    }

}
