<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Request;
use Gate;

class OrderController extends Controller

{
    protected $base_url = '/admin/orders';
    protected $view_path = 'admin.orders';

    public function __construct()
    {
    }

    public function index()
    {
        if (Gate::denies('@article')) {
            return abort(403);
        }

        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        if (Gate::denies('@article-create')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        return view('admin.orders.create', ['base_url' => $this->base_url]);
    }

    public function edit($id)
    {
        if (Gate::denies('@article-edit')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $activity = Order::find($id);
        $activity->images = null;

        return view('admin.orders.edit', ['content' => $activity, 'base_url' => $this->base_url, 'back_url' => $this->base_url]);
    }

    public function store()
    {
        $input = Request::all();
        $input['user_id'] = Auth::user()->id;
        $activity = Order::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '活动', $activity->id, ''));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url);
    }

    public function update($id)
    {
        $input = Request::all();

        $activity = Order::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '活动', $activity->id, ''));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url);
    }

    public function save($id)
    {
        $activity = Order::find($id);

        if (empty($activity)) {
            return;
        }

        $activity->update(Request::all());
    }

    public function sort()
    {
        return Order::sort();
    }

    public function state()
    {
        $input = request()->all();
        Order::state($input);

        $ids = $input['ids'];
        $stateName = Order::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '活动' . UserLog::ACTION_STATE . ':' . $stateName, $id, ''));
        }
    }

    public function table()
    {
        return Order::table();
    }

}